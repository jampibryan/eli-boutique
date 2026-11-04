<?php

namespace App\Services;

use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProductoExcelService
{
    /**
     * Genera el archivo Excel (.xlsx) con diseño corporativo y logotipo de Eli Boutique.
     *
     * @return string Ruta absoluta del archivo temporal .xlsx
     */
    public function generarExcel(): string
    {
        $productos = Producto::with(['categoriaProducto', 'productoGenero', 'tallaStocks.talla'])
            ->get()
            ->sortBy('categoriaProducto.nombreCP');

        // Cálculo de métricas y agrupación
        $totalProductos = $productos->count();
        $stockTotal = $productos->sum(fn($p) => $p->tallaStocks->sum('stock'));
        $valorInventario = $productos->sum(fn($p) => $p->tallaStocks->sum('stock') * $p->precioP);

        // Agrupación por categoría
        $productosPorCategoria = $productos->groupBy(fn($p) => $p->categoriaProducto->nombreCP ?? 'Sin categoría');
        $totalCategorias = $productosPorCategoria->count();

        // Resumen por categoría
        $resumenCategorias = [];
        foreach ($productosPorCategoria as $categoria => $prods) {
            $stockCat = $prods->sum(fn($p) => $p->tallaStocks->sum('stock'));
            $valorCat = $prods->sum(fn($p) => $p->tallaStocks->sum('stock') * $p->precioP);
            $resumenCategorias[$categoria] = [
                'categoria' => $categoria,
                'productos' => $prods->count(),
                'stock' => $stockCat,
                'valor' => $valorCat,
                'porcentajeStock' => $stockTotal > 0 ? ($stockCat / $stockTotal) : 0,
            ];
        }

        // Verificar existencia del logotipo
        $logoPath = public_path('img/logo_eli_boutique.png');
        $hasLogo = file_exists($logoPath);

        // Generar XMLs de las hojas
        $sheet1Xml = $this->buildSheetDetalle($productosPorCategoria, $totalProductos, $totalCategorias, $stockTotal, $valorInventario, $hasLogo);
        $sheet2Xml = $this->buildSheetResumen($resumenCategorias, $totalProductos, $stockTotal, $valorInventario, $hasLogo);

        $files = [
            '[Content_Types].xml' => $this->getContentTypesXml($hasLogo),
            '_rels/.rels' => $this->getRootRelsXml(),
            'xl/_rels/workbook.xml.rels' => $this->getWorkbookRelsXml(),
            'xl/workbook.xml' => $this->getWorkbookXml(),
            'xl/styles.xml' => $this->getStylesXml(),
            'xl/worksheets/sheet1.xml' => $sheet1Xml,
            'xl/worksheets/sheet2.xml' => $sheet2Xml,
        ];

        // Agregar recursos de dibujo y relaciones si existe el logotipo
        if ($hasLogo) {
            $files['xl/worksheets/_rels/sheet1.xml.rels'] = $this->getSheetDrawingRelsXml(1);
            $files['xl/worksheets/_rels/sheet2.xml.rels'] = $this->getSheetDrawingRelsXml(2);
            $files['xl/drawings/drawing1.xml'] = $this->getDrawingXml();
            $files['xl/drawings/drawing2.xml'] = $this->getDrawingXml();
            $files['xl/drawings/_rels/drawing1.xml.rels'] = $this->getDrawingImageRelsXml();
            $files['xl/drawings/_rels/drawing2.xml.rels'] = $this->getDrawingImageRelsXml();
            $files['xl/media/image1.png'] = file_get_contents($logoPath);
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'eli_prod_') . '.xlsx';
        $this->createZip($tempPath, $files);

        return $tempPath;
    }

    /**
     * Construye la hoja 1: Detalle de Productos
     */
    protected function buildSheetDetalle(Collection $productosPorCategoria, int $totalProductos, int $totalCategorias, float|int $stockTotal, float $valorInventario, bool $hasLogo): string
    {
        $fechaGeneracion = Carbon::now('America/Lima')->format('d/m/Y h:i A');
        $rows = [];
        $merges = [];
        $currentRow = 1;

        // Fila 1: Logo (Col A) y Nombre Corporativo (Cols B:H)
        $rows[] = $this->rowXml($currentRow, [
            $this->cellEmpty('A' . $currentRow, 0),
            $this->cellStr('B' . $currentRow, 'ELI BOUTIQUE', 31),
            $this->cellEmpty('C' . $currentRow, 31),
            $this->cellEmpty('D' . $currentRow, 31),
            $this->cellEmpty('E' . $currentRow, 31),
            $this->cellEmpty('F' . $currentRow, 31),
            $this->cellEmpty('G' . $currentRow, 31),
            $this->cellEmpty('H' . $currentRow, 31),
        ], 28);
        $merges[] = 'B' . $currentRow . ':H' . $currentRow;
        $currentRow++;

        // Fila 2: RUC y Dirección
        $rows[] = $this->rowXml($currentRow, [
            $this->cellEmpty('A' . $currentRow, 0),
            $this->cellStr('B' . $currentRow, 'RUC: 20276544711  •  Calle Ayacucho 624, Pacanga', 32),
            $this->cellEmpty('C' . $currentRow, 32),
            $this->cellEmpty('D' . $currentRow, 32),
            $this->cellEmpty('E' . $currentRow, 32),
            $this->cellEmpty('F' . $currentRow, 32),
            $this->cellEmpty('G' . $currentRow, 32),
            $this->cellEmpty('H' . $currentRow, 32),
        ], 18);
        $merges[] = 'B' . $currentRow . ':H' . $currentRow;
        $currentRow++;

        // Fila 3: Contacto y Email
        $rows[] = $this->rowXml($currentRow, [
            $this->cellEmpty('A' . $currentRow, 0),
            $this->cellStr('B' . $currentRow, 'Celular: 922 070 116  •  Email: inventario@eliboutique.pe', 32),
            $this->cellEmpty('C' . $currentRow, 32),
            $this->cellEmpty('D' . $currentRow, 32),
            $this->cellEmpty('E' . $currentRow, 32),
            $this->cellEmpty('F' . $currentRow, 32),
            $this->cellEmpty('G' . $currentRow, 32),
            $this->cellEmpty('H' . $currentRow, 32),
        ], 18);
        $merges[] = 'B' . $currentRow . ':H' . $currentRow;
        $currentRow++;

        // Fila 4: Fecha de emisión y total de productos
        $rows[] = $this->rowXml($currentRow, [
            $this->cellEmpty('A' . $currentRow, 0),
            $this->cellStr('B' . $currentRow, 'Reporte emitido: ' . $fechaGeneracion . ' (PET UTC-5)  •  Total catálogo: ' . $totalProductos . ' productos', 33),
            $this->cellEmpty('C' . $currentRow, 33),
            $this->cellEmpty('D' . $currentRow, 33),
            $this->cellEmpty('E' . $currentRow, 33),
            $this->cellEmpty('F' . $currentRow, 33),
            $this->cellEmpty('G' . $currentRow, 33),
            $this->cellEmpty('H' . $currentRow, 33),
        ], 18);
        $merges[] = 'B' . $currentRow . ':H' . $currentRow;
        $currentRow++;

        // Fila 5: Separador visual
        $rows[] = $this->rowXml($currentRow, [], 10);
        $currentRow++;

        // Fila 6: Banner Principal
        $rows[] = $this->rowXml($currentRow, [
            $this->cellStr('A' . $currentRow, 'REPORTE GENERAL DE PRODUCTOS E INVENTARIO', 1),
            $this->cellEmpty('B' . $currentRow, 1),
            $this->cellEmpty('C' . $currentRow, 1),
            $this->cellEmpty('D' . $currentRow, 1),
            $this->cellEmpty('E' . $currentRow, 1),
            $this->cellEmpty('F' . $currentRow, 1),
            $this->cellEmpty('G' . $currentRow, 1),
            $this->cellEmpty('H' . $currentRow, 1),
        ], 28);
        $merges[] = 'A' . $currentRow . ':H' . $currentRow;
        $currentRow++;

        // Fila 7: Separador
        $rows[] = $this->rowXml($currentRow, [], 10);
        $currentRow++;

        // Fila 8-9: Bloque de KPIs
        $rows[] = $this->rowXml($currentRow, [
            $this->cellStr('A' . $currentRow, 'TOTAL PRODUCTOS', 4),
            $this->cellEmpty('B' . $currentRow, 4),
            $this->cellStr('C' . $currentRow, 'CATEGORÍAS', 6),
            $this->cellEmpty('D' . $currentRow, 6),
            $this->cellStr('E' . $currentRow, 'STOCK TOTAL (UDS)', 8),
            $this->cellEmpty('F' . $currentRow, 8),
            $this->cellStr('G' . $currentRow, 'VALOR DEL INVENTARIO', 10),
            $this->cellEmpty('H' . $currentRow, 10),
        ], 20);
        $merges[] = 'A' . $currentRow . ':B' . $currentRow;
        $merges[] = 'C' . $currentRow . ':D' . $currentRow;
        $merges[] = 'E' . $currentRow . ':F' . $currentRow;
        $merges[] = 'G' . $currentRow . ':H' . $currentRow;
        $currentRow++;

        $rows[] = $this->rowXml($currentRow, [
            $this->cellNum('A' . $currentRow, $totalProductos, 5),
            $this->cellEmpty('B' . $currentRow, 5),
            $this->cellNum('C' . $currentRow, $totalCategorias, 7),
            $this->cellEmpty('D' . $currentRow, 7),
            $this->cellNum('E' . $currentRow, $stockTotal, 9),
            $this->cellEmpty('F' . $currentRow, 9),
            $this->cellNum('G' . $currentRow, $valorInventario, 11),
            $this->cellEmpty('H' . $currentRow, 11),
        ], 26);
        $merges[] = 'A' . $currentRow . ':B' . $currentRow;
        $merges[] = 'C' . $currentRow . ':D' . $currentRow;
        $merges[] = 'E' . $currentRow . ':F' . $currentRow;
        $merges[] = 'G' . $currentRow . ':H' . $currentRow;
        $currentRow++;

        // Fila 10: Separador
        $rows[] = $this->rowXml($currentRow, [], 14);
        $currentRow++;

        // Detalle por Categoría
        foreach ($productosPorCategoria as $categoria => $prodsCat) {
            // Título de la categoría
            $rows[] = $this->rowXml($currentRow, [
                $this->cellStr('A' . $currentRow, 'CATEGORÍA: ' . strtoupper($categoria) . ' (' . $prodsCat->count() . ' productos)', 12),
                $this->cellEmpty('B' . $currentRow, 12),
                $this->cellEmpty('C' . $currentRow, 12),
                $this->cellEmpty('D' . $currentRow, 12),
                $this->cellEmpty('E' . $currentRow, 12),
                $this->cellEmpty('F' . $currentRow, 12),
                $this->cellEmpty('G' . $currentRow, 12),
                $this->cellEmpty('H' . $currentRow, 12),
            ], 24);
            $merges[] = 'A' . $currentRow . ':H' . $currentRow;
            $currentRow++;

            // Encabezados de tabla
            $rows[] = $this->rowXml($currentRow, [
                $this->cellStr('A' . $currentRow, 'CÓDIGO', 13),
                $this->cellStr('B' . $currentRow, 'DESCRIPCIÓN', 13),
                $this->cellStr('C' . $currentRow, 'CATEGORÍA', 13),
                $this->cellStr('D' . $currentRow, 'GÉNERO', 13),
                $this->cellStr('E' . $currentRow, 'PRECIO UNIT. (S/)', 13),
                $this->cellStr('F' . $currentRow, 'STOCK (UDS)', 13),
                $this->cellStr('G' . $currentRow, 'TALLAS (STOCK)', 13),
                $this->cellStr('H' . $currentRow, 'VALORIZACIÓN (S/)', 13),
            ], 22);
            $currentRow++;

            $subtotalStockCat = 0;
            $subtotalValorCat = 0;
            $isZebra = false;

            foreach ($prodsCat as $prod) {
                $stockProd = $prod->tallaStocks->sum('stock');
                $valorProd = $stockProd * $prod->precioP;
                $subtotalStockCat += $stockProd;
                $subtotalValorCat += $valorProd;

                // Armar string de tallas
                $tallasText = [];
                if ($prod->tallaStocks && $prod->tallaStocks->count()) {
                    foreach ($prod->tallaStocks as $ts) {
                        $tallasText[] = ($ts->talla->descripcion ?? '-') . ': ' . $ts->stock;
                    }
                    $tallasStr = implode(' | ', $tallasText);
                } else {
                    $tallasStr = 'Sin tallas';
                }

                $sCode = $isZebra ? 28 : 16;
                $sCenter = $isZebra ? 27 : 15; // Centrado para descripción, categoría, género y tallas
                $sCurr = $isZebra ? 29 : 17;
                $sInt = $isZebra ? 30 : 18;

                $rows[] = $this->rowXml($currentRow, [
                    $this->cellStr('A' . $currentRow, $prod->codigoP ?? '-', $sCode),
                    $this->cellStr('B' . $currentRow, $prod->descripcionP ?? '-', $sCenter), // DESCRIPCIÓN CENTRADA
                    $this->cellStr('C' . $currentRow, $categoria, $sCenter),
                    $this->cellStr('D' . $currentRow, $prod->productoGenero->descripcionPG ?? '-', $sCenter),
                    $this->cellNum('E' . $currentRow, $prod->precioP, $sCurr),
                    $this->cellNum('F' . $currentRow, $stockProd, $sInt),
                    $this->cellStr('G' . $currentRow, $tallasStr, $sCenter), // TALLAS (STOCK) CENTRADO
                    $this->cellNum('H' . $currentRow, $valorProd, $sCurr),
                ], 20);

                $isZebra = !$isZebra;
                $currentRow++;
            }

            // Fila de Subtotal de Categoría
            $rows[] = $this->rowXml($currentRow, [
                $this->cellStr('A' . $currentRow, 'SUBTOTAL ' . strtoupper($categoria), 19),
                $this->cellEmpty('B' . $currentRow, 19),
                $this->cellEmpty('C' . $currentRow, 19),
                $this->cellEmpty('D' . $currentRow, 19),
                $this->cellEmpty('E' . $currentRow, 19),
                $this->cellNum('F' . $currentRow, $subtotalStockCat, 20),
                $this->cellEmpty('G' . $currentRow, 19),
                $this->cellNum('H' . $currentRow, $subtotalValorCat, 21),
            ], 22);
            $merges[] = 'A' . $currentRow . ':E' . $currentRow;
            $currentRow++;

            // Fila en blanco de separación
            $rows[] = $this->rowXml($currentRow, [], 10);
            $currentRow++;
        }

        // Fila de TOTAL GENERAL
        $rows[] = $this->rowXml($currentRow, [
            $this->cellStr('A' . $currentRow, 'TOTAL GENERAL DEL INVENTARIO', 22),
            $this->cellEmpty('B' . $currentRow, 22),
            $this->cellEmpty('C' . $currentRow, 22),
            $this->cellEmpty('D' . $currentRow, 22),
            $this->cellEmpty('E' . $currentRow, 22),
            $this->cellNum('F' . $currentRow, $stockTotal, 23),
            $this->cellEmpty('G' . $currentRow, 22),
            $this->cellNum('H' . $currentRow, $valorInventario, 24),
        ], 26);
        $merges[] = 'A' . $currentRow . ':E' . $currentRow;

        // Anchos de columna
        $cols = '
    <cols>
      <col min="1" max="1" width="16" customWidth="1"/>
      <col min="2" max="2" width="36" customWidth="1"/>
      <col min="3" max="3" width="22" customWidth="1"/>
      <col min="4" max="4" width="15" customWidth="1"/>
      <col min="5" max="5" width="18" customWidth="1"/>
      <col min="6" max="6" width="15" customWidth="1"/>
      <col min="7" max="7" width="38" customWidth="1"/>
      <col min="8" max="8" width="22" customWidth="1"/>
    </cols>';

        return $this->wrapSheetXml($cols, implode("\n", $rows), $merges, $hasLogo ? 'rId1' : null);
    }

    /**
     * Construye la hoja 2: Resumen por Categoría
     */
    protected function buildSheetResumen(array $resumenCategorias, int $totalProductos, float|int $stockTotal, float $valorInventario, bool $hasLogo): string
    {
        $fechaGeneracion = Carbon::now('America/Lima')->format('d/m/Y h:i A');
        $rows = [];
        $merges = [];
        $currentRow = 1;

        // Fila 1: Logo y Nombre
        $rows[] = $this->rowXml($currentRow, [
            $this->cellEmpty('A' . $currentRow, 0),
            $this->cellStr('B' . $currentRow, 'ELI BOUTIQUE', 31),
            $this->cellEmpty('C' . $currentRow, 31),
            $this->cellEmpty('D' . $currentRow, 31),
            $this->cellEmpty('E' . $currentRow, 31),
        ], 28);
        $merges[] = 'B' . $currentRow . ':E' . $currentRow;
        $currentRow++;

        // Fila 2: Subtítulo
        $rows[] = $this->rowXml($currentRow, [
            $this->cellEmpty('A' . $currentRow, 0),
            $this->cellStr('B' . $currentRow, 'RUC: 20276544711  •  Calle Ayacucho 624, Pacanga', 32),
            $this->cellEmpty('C' . $currentRow, 32),
            $this->cellEmpty('D' . $currentRow, 32),
            $this->cellEmpty('E' . $currentRow, 32),
        ], 18);
        $merges[] = 'B' . $currentRow . ':E' . $currentRow;
        $currentRow++;

        // Fila 3: Contacto
        $rows[] = $this->rowXml($currentRow, [
            $this->cellEmpty('A' . $currentRow, 0),
            $this->cellStr('B' . $currentRow, 'Celular: 922 070 116  •  inventario@eliboutique.pe', 32),
            $this->cellEmpty('C' . $currentRow, 32),
            $this->cellEmpty('D' . $currentRow, 32),
            $this->cellEmpty('E' . $currentRow, 32),
        ], 18);
        $merges[] = 'B' . $currentRow . ':E' . $currentRow;
        $currentRow++;

        // Fila 4: Fecha
        $rows[] = $this->rowXml($currentRow, [
            $this->cellEmpty('A' . $currentRow, 0),
            $this->cellStr('B' . $currentRow, 'Reporte emitido: ' . $fechaGeneracion . ' (PET UTC-5)', 33),
            $this->cellEmpty('C' . $currentRow, 33),
            $this->cellEmpty('D' . $currentRow, 33),
            $this->cellEmpty('E' . $currentRow, 33),
        ], 18);
        $merges[] = 'B' . $currentRow . ':E' . $currentRow;
        $currentRow++;

        // Fila 5: Separador
        $rows[] = $this->rowXml($currentRow, [], 10);
        $currentRow++;

        // Fila 6: Título
        $rows[] = $this->rowXml($currentRow, [
            $this->cellStr('A' . $currentRow, 'RESUMEN EJECUTIVO Y PARTICIPACIÓN POR CATEGORÍA', 1),
            $this->cellEmpty('B' . $currentRow, 1),
            $this->cellEmpty('C' . $currentRow, 1),
            $this->cellEmpty('D' . $currentRow, 1),
            $this->cellEmpty('E' . $currentRow, 1),
        ], 28);
        $merges[] = 'A' . $currentRow . ':E' . $currentRow;
        $currentRow++;

        // Fila 7: Separador
        $rows[] = $this->rowXml($currentRow, [], 14);
        $currentRow++;

        // Fila 8: Encabezados de tabla
        $rows[] = $this->rowXml($currentRow, [
            $this->cellStr('A' . $currentRow, 'CATEGORÍA', 13),
            $this->cellStr('B' . $currentRow, 'CANT. PRODUCTOS', 13),
            $this->cellStr('C' . $currentRow, 'STOCK TOTAL (UDS)', 13),
            $this->cellStr('D' . $currentRow, 'VALOR INVENTARIO (S/)', 13),
            $this->cellStr('E' . $currentRow, '% PARTICIPACIÓN STOCK', 13),
        ], 24);
        $currentRow++;

        $isZebra = false;
        foreach ($resumenCategorias as $cat) {
            $sCenter = $isZebra ? 27 : 15;
            $sInt = $isZebra ? 30 : 18;
            $sCurr = $isZebra ? 29 : 17;
            $sPct = 25;

            $rows[] = $this->rowXml($currentRow, [
                $this->cellStr('A' . $currentRow, $cat['categoria'], $sCenter),
                $this->cellNum('B' . $currentRow, $cat['productos'], $sInt),
                $this->cellNum('C' . $currentRow, $cat['stock'], $sInt),
                $this->cellNum('D' . $currentRow, $cat['valor'], $sCurr),
                $this->cellNum('E' . $currentRow, round($cat['porcentajeStock'], 4), $sPct),
            ], 20);

            $isZebra = !$isZebra;
            $currentRow++;
        }

        // Fila de Totales
        $rows[] = $this->rowXml($currentRow, [
            $this->cellStr('A' . $currentRow, 'TOTAL GENERAL', 22),
            $this->cellNum('B' . $currentRow, $totalProductos, 23),
            $this->cellNum('C' . $currentRow, $stockTotal, 23),
            $this->cellNum('D' . $currentRow, $valorInventario, 24),
            $this->cellNum('E' . $currentRow, 1.0, 25),
        ], 26);

        $cols = '
    <cols>
      <col min="1" max="1" width="30" customWidth="1"/>
      <col min="2" max="2" width="22" customWidth="1"/>
      <col min="3" max="3" width="22" customWidth="1"/>
      <col min="4" max="4" width="26" customWidth="1"/>
      <col min="5" max="5" width="24" customWidth="1"/>
    </cols>';

        return $this->wrapSheetXml($cols, implode("\n", $rows), $merges, $hasLogo ? 'rId1' : null);
    }

    // === Helpers para construcción de celdas y filas XML ===

    protected function rowXml(int $rowIndex, array $cells, float $height = 18): string
    {
        return '<row r="' . $rowIndex . '" ht="' . $height . '" customHeight="1">' . implode('', $cells) . '</row>';
    }

    protected function cellStr(string $ref, string $text, int $styleId = 0): string
    {
        $escaped = htmlspecialchars($text, ENT_XML1, 'UTF-8');
        return '<c r="' . $ref . '" t="inlineStr" s="' . $styleId . '"><is><t>' . $escaped . '</t></is></c>';
    }

    protected function cellNum(string $ref, int|float $number, int $styleId = 0): string
    {
        return '<c r="' . $ref . '" s="' . $styleId . '"><v>' . $number . '</v></c>';
    }

    protected function cellEmpty(string $ref, int $styleId = 0): string
    {
        return '<c r="' . $ref . '" s="' . $styleId . '"/>';
    }

    protected function wrapSheetXml(string $cols, string $sheetData, array $merges = [], ?string $drawingRId = null): string
    {
        $mergesXml = '';
        if (!empty($merges)) {
            $mergesXml = '<mergeCells count="' . count($merges) . '">';
            foreach ($merges as $ref) {
                $mergesXml .= '<mergeCell ref="' . $ref . '"/>';
            }
            $mergesXml .= '</mergeCells>';
        }

        $drawingXml = $drawingRId ? '<drawing r:id="' . $drawingRId . '"/>' : '';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheetViews>
    <sheetView tabSelected="1" workbookViewId="0">
      <pane ySplit="0" topLeftCell="A1" activePane="topLeft" state="split"/>
    </sheetView>
  </sheetViews>
  <sheetFormatPr defaultRowHeight="18"/>' . $cols . '
  <sheetData>' . "\n" . $sheetData . "\n" . '  </sheetData>' . "\n" . $mergesXml . '
  <pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>
  ' . $drawingXml . '
</worksheet>';
    }

    // === Estructura de OpenXML y Estilos ===

    protected function getContentTypesXml(bool $hasLogo): string
    {
        $drawingsXml = '';
        if ($hasLogo) {
            $drawingsXml = '  <Default Extension="png" ContentType="image/png"/>' . "\n" .
                           '  <Override PartName="/xl/drawings/drawing1.xml" ContentType="application/vnd.openxmlformats-officedocument.drawing+xml"/>' . "\n" .
                           '  <Override PartName="/xl/drawings/drawing2.xml" ContentType="application/vnd.openxmlformats-officedocument.drawing+xml"/>' . "\n";
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
' . $drawingsXml . '  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';
    }

    protected function getRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
    }

    protected function getWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
    }

    protected function getWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Catálogo Detallado" sheetId="1" r:id="rId1"/>
    <sheet name="Resumen por Categoría" sheetId="2" r:id="rId2"/>
  </sheets>
</workbook>';
    }

    protected function getSheetDrawingRelsXml(int $drawingNum): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing" Target="../drawings/drawing' . $drawingNum . '.xml"/>
</Relationships>';
    }

    protected function getDrawingImageRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/image1.png"/>
</Relationships>';
    }

    protected function getDrawingXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<xdr:wsDr xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">
  <xdr:oneCellAnchor>
    <xdr:from>
      <xdr:col>0</xdr:col>
      <xdr:colOff>100000</xdr:colOff>
      <xdr:row>0</xdr:row>
      <xdr:rowOff>40000</xdr:rowOff>
    </xdr:from>
    <xdr:ext cx="850000" cy="850000"/>
    <xdr:pic>
      <xdr:nvPicPr>
        <xdr:cNvPr id="2" name="Logo Eli Boutique"/>
        <xdr:cNvPicPr>
          <a:picLocks noChangeAspect="1"/>
        </xdr:cNvPicPr>
      </xdr:nvPicPr>
      <xdr:blipFill>
        <a:blip xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" r:embed="rId1"/>
        <a:stretch>
          <a:fillRect/>
        </a:stretch>
      </xdr:blipFill>
      <xdr:spPr>
        <a:xfrm>
          <a:off x="0" y="0"/>
          <a:ext cx="850000" cy="850000"/>
        </a:xfrm>
        <a:prstGeom geom="rect">
          <a:avLst/>
        </a:prstGeom>
      </xdr:spPr>
    </xdr:pic>
    <xdr:clientData/>
  </xdr:oneCellAnchor>
</xdr:wsDr>';
    }

    protected function getStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <numFmts count="3">
    <numFmt numFmtId="164" formatCode="&quot;S/ &quot;#,##0.00"/>
    <numFmt numFmtId="165" formatCode="#,##0"/>
    <numFmt numFmtId="166" formatCode="0.0%"/>
  </numFmts>
  <fonts count="13">
    <!-- 0: Regular base -->
    <font><sz val="10"/><name val="Segoe UI"/><color rgb="FF333333"/></font>
    <!-- 1: Bold base -->
    <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF333333"/></font>
    <!-- 2: Main title white -->
    <font><b/><sz val="13"/><name val="Segoe UI"/><color rgb="FFFFFFFF"/></font>
    <!-- 3: Company brand purple -->
    <font><b/><sz val="15"/><name val="Segoe UI"/><color rgb="FF4F46E5"/></font>
    <!-- 4: Header white bold -->
    <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FFFFFFFF"/></font>
    <!-- 5: Category title purple bold -->
    <font><b/><sz val="11"/><name val="Segoe UI"/><color rgb="FF4F46E5"/></font>
    <!-- 6: KPI Label gray -->
    <font><b/><sz val="8"/><name val="Segoe UI"/><color rgb="FF6B7280"/></font>
    <!-- 7: Code bold purple -->
    <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF667EEA"/></font>
    <!-- 8: Total bold dark -->
    <font><b/><sz val="11"/><name val="Segoe UI"/><color rgb="FF1F2937"/></font>
    <!-- 9: Header Company 20pt -->
    <font><b/><sz val="20"/><name val="Segoe UI"/><color rgb="FF4F46E5"/></font>
    <!-- 10: Header Subtitle 10pt gray -->
    <font><sz val="10"/><name val="Segoe UI"/><color rgb="FF4B5563"/></font>
    <!-- 11: Header Date 9.5pt gray -->
    <font><sz val="9.5"/><name val="Segoe UI"/><color rgb="FF6B7280"/></font>
    <!-- 12: KPI Values Colors -->
    <font><b/><sz val="15"/><name val="Segoe UI"/><color rgb="FF10B981"/></font>
  </fonts>
  <fills count="12">
    <!-- 0: None -->
    <fill><patternFill patternType="none"/></fill>
    <!-- 1: Gray125 -->
    <fill><patternFill patternType="gray125"/></fill>
    <!-- 2: Primary Purple #667EEA -->
    <fill><patternFill patternType="solid"><fgColor rgb="FF667EEA"/></patternFill></fill>
    <!-- 3: Category Header Light Purple #EEF2FF -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFEEF2FF"/></patternFill></fill>
    <!-- 4: Subtotal Row Light #E0E7FF -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFE0E7FF"/></patternFill></fill>
    <!-- 5: Grand Total Gold #FDE68A -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFFDE68A"/></patternFill></fill>
    <!-- 6: KPI Box Purple #EEF2FF -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFEEF2FF"/></patternFill></fill>
    <!-- 7: KPI Box Green #ECFDF5 -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFECFDF5"/></patternFill></fill>
    <!-- 8: KPI Box Orange #FFFBEB -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFFFFBEB"/></patternFill></fill>
    <!-- 9: KPI Box Red #FEF2F2 -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFFEF2F2"/></patternFill></fill>
    <!-- 10: Zebra light gray #F9FAFB -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFF9FAFB"/></patternFill></fill>
    <!-- 11: Dark brand #2C2C2C -->
    <fill><patternFill patternType="solid"><fgColor rgb="FF2C2C2C"/></patternFill></fill>
  </fills>
  <borders count="3">
    <!-- 0: none -->
    <border><left/><right/><top/><bottom/></border>
    <!-- 1: thin all around -->
    <border>
      <left style="thin"><color rgb="FFE5E7EB"/></left>
      <right style="thin"><color rgb="FFE5E7EB"/></right>
      <top style="thin"><color rgb="FFE5E7EB"/></top>
      <bottom style="thin"><color rgb="FFE5E7EB"/></bottom>
    </border>
    <!-- 2: top thin, bottom double -->
    <border>
      <left style="thin"><color rgb="FFD1D5DB"/></left>
      <right style="thin"><color rgb="FFD1D5DB"/></right>
      <top style="thin"><color rgb="FF9CA3AF"/></top>
      <bottom style="double"><color rgb="FF1F2937"/></bottom>
    </border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="34">
    <!-- 0: Default -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <!-- 1: Main Title Banner -->
    <xf numFmtId="0" fontId="2" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 2: Brand Header -->
    <xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>
    <!-- 3: Subtitle / info -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyFont="1"/>
    <!-- 4: KPI Label Purple -->
    <xf numFmtId="0" fontId="6" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 5: KPI Value Purple -->
    <xf numFmtId="165" fontId="3" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 6: KPI Label Green -->
    <xf numFmtId="0" fontId="6" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 7: KPI Value Green -->
    <xf numFmtId="165" fontId="12" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 8: KPI Label Orange -->
    <xf numFmtId="0" fontId="6" fillId="8" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 9: KPI Value Orange -->
    <xf numFmtId="165" fontId="3" fillId="8" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 10: KPI Label Red -->
    <xf numFmtId="0" fontId="6" fillId="9" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 11: KPI Value Red -->
    <xf numFmtId="164" fontId="3" fillId="9" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 12: Category Title Header -->
    <xf numFmtId="0" fontId="5" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" indent="1"/></xf>
    <!-- 13: Table Column Header -->
    <xf numFmtId="0" fontId="4" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
    <!-- 14: Data Text Left -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
    <!-- 15: Data Text Center -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 16: Data Code Bold Center -->
    <xf numFmtId="0" fontId="7" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 17: Data Currency Right -->
    <xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyFill="0" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <!-- 18: Data Integer Center -->
    <xf numFmtId="165" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 19: Subtotal Label Right -->
    <xf numFmtId="0" fontId="1" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <!-- 20: Subtotal Integer Center -->
    <xf numFmtId="165" fontId="1" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 21: Subtotal Currency Right -->
    <xf numFmtId="164" fontId="1" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <!-- 22: Grand Total Label Right -->
    <xf numFmtId="0" fontId="8" fillId="5" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <!-- 23: Grand Total Integer Center -->
    <xf numFmtId="165" fontId="8" fillId="5" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 24: Grand Total Currency Right -->
    <xf numFmtId="164" fontId="8" fillId="5" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <!-- 25: Data Percentage Center -->
    <xf numFmtId="166" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 26: Data Text Left Zebra -->
    <xf numFmtId="0" fontId="0" fillId="10" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
    <!-- 27: Data Text Center Zebra -->
    <xf numFmtId="0" fontId="0" fillId="10" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 28: Data Code Bold Zebra -->
    <xf numFmtId="0" fontId="7" fillId="10" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 29: Data Currency Right Zebra -->
    <xf numFmtId="164" fontId="0" fillId="10" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <!-- 30: Data Integer Center Zebra -->
    <xf numFmtId="165" fontId="0" fillId="10" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 31: Header Brand 20pt Center -->
    <xf numFmtId="0" fontId="9" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 32: Header Info 10pt Center -->
    <xf numFmtId="0" fontId="10" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 33: Header Date 9.5pt Center -->
    <xf numFmtId="0" fontId="11" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  </cellXfs>
</styleSheet>';
    }

    /**
     * Empaqueta el archivo ZIP usando ZipArchive o PhpZip como fallback.
     */
    protected function createZip(string $zipPath, array $files): void
    {
        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                foreach ($files as $name => $content) {
                    $zip->addFromString($name, $content);
                }
                $zip->close();
                return;
            }
        }

        if (class_exists(\PhpZip\ZipFile::class)) {
            $zip = new \PhpZip\ZipFile();
            foreach ($files as $name => $content) {
                $zip->addFromString($name, $content);
            }
            $zip->saveAsFile($zipPath);
            $zip->close();
            return;
        }

        throw new \RuntimeException('No se encontró soporte para compresión ZIP (ZipArchive o PhpZip) en el entorno PHP.');
    }
}
