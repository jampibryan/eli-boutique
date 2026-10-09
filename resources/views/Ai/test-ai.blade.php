@extends('adminlte::page')

@section('title', 'Asistente IA Copilot — Eli Boutique')

@section('content_header')
{{-- HERO HEADER LUXURY BOUTIQUE --}}
<div class="eli-ai-hero-header rounded-xl mb-3 p-4 shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); border-bottom: 2.5px solid #d4af37; color: white;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="mr-3 bg-white p-2 rounded-circle shadow" style="width: 62px; height: 62px; display: flex; align-items: center; justify-content: center; border: 2.5px solid #d4af37;">
                <img src="{{ asset('img/logo_eli_boutique.png') }}" alt="Eli Boutique" style="max-height: 44px; max-width: 44px; object-fit: contain;">
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h1 class="m-0 font-weight-bold" style="font-size: 1.65rem; background: linear-gradient(135deg, #ffffff 40%, #fde047 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.5px;">
                        Eli Boutique AI Copilot
                    </h1>
                    <span class="badge ml-2" style="background: linear-gradient(135deg, #d4af37, #b45309); color: #ffffff; font-size: 0.72rem; font-weight: 800; padding: 4px 8px; border-radius: 6px; letter-spacing: 0.5px;">
                        DEEPSEEK V3 ENGINE
                    </span>
                </div>
                <p class="m-0" style="color: #cbd5e1; font-size: 0.88rem;">
                    Copiloto ejecutivo y asesor de modas con trazabilidad 360° en <strong>Inventario por Talla</strong>, <strong>Caja Diaria</strong> y <strong>Ventas POS</strong>.
                </p>
            </div>
        </div>

        {{-- METRICS & BACKEND STATUS CHIPS --}}
        <div class="d-flex align-items-center flex-wrap gap-2 mt-2 mt-md-0">
            <div class="px-3 py-2 rounded-lg d-flex align-items-center" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px);">
                <span class="text-light mr-2 font-weight-bold" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    <i class="fas fa-server mr-1 text-warning"></i> Motor:
                </span>
                <span id="ai-status-badge" class="badge badge-warning px-2 py-1" style="font-size: 0.76rem;">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Conectando
                </span>
                <button class="btn btn-sm btn-link text-light ml-2 p-0" onclick="checkBackendHealth()" title="Reverificar estado del servidor">
                    <i class="fas fa-sync-alt" style="font-size: 0.75rem;"></i>
                </button>
            </div>

            <div class="px-3 py-2 rounded-lg d-flex align-items-center d-none d-sm-flex" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);">
                <i class="fas fa-clock text-info mr-2" style="font-size: 0.85rem;"></i>
                <span style="font-size: 0.78rem; color: #e2e8f0;">Zona PET (UTC-5)</span>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-0">

    {{-- NAVEGACIÓN POR PESTAÑAS (PILLS MODERNAS) --}}
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 14px; overflow: hidden; background: #ffffff;">
        <div class="card-header p-2 bg-white border-bottom" style="border-color: #f1f5f9 !important;">
            <ul class="nav nav-pills eli-nav-pills d-flex" id="aiTab" role="tablist">
                <li class="nav-item flex-fill text-center">
                    <a class="nav-link active font-weight-bold py-2 px-3" id="tab-copilot-link" data-toggle="pill" href="#tab-copilot" role="tab">
                        <i class="fas fa-sparkles mr-1 text-warning"></i> Copiloto Ejecutivo (Chat Interactivo)
                    </a>
                </li>
                <li class="nav-item flex-fill text-center">
                    <a class="nav-link font-weight-bold py-2 px-3" id="tab-rag-link" data-toggle="pill" href="#tab-rag" role="tab">
                        <i class="fas fa-database mr-1 text-info"></i> Inspector RAG & Contexto de Negocio
                    </a>
                </li>
                <li class="nav-item flex-fill text-center">
                    <a class="nav-link font-weight-bold py-2 px-3" id="tab-libre-link" data-toggle="pill" href="#tab-libre" role="tab">
                        <i class="fas fa-tshirt mr-1 text-danger"></i> Tendencias & Asesoría de Modas
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-3 p-md-4">
            <div class="tab-content" id="aiTabContent">
                
                {{-- TAB 1: COPILOT INTERACTIVO --}}
                <div class="tab-pane fade show active" id="tab-copilot" role="tabpanel">
                    
                    {{-- Barra superior de contexto colapsable --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-2 px-3 rounded-lg border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-sm btn-outline-secondary font-weight-bold mr-2" type="button" data-toggle="collapse" data-target="#contextCollapse" style="border-radius: 8px;">
                                    <i class="fas fa-sliders-h mr-1 text-primary"></i> Contexto ERP RAG en Vivo <i class="fas fa-chevron-down ml-1"></i>
                                </button>
                                <span class="badge badge-light border text-muted px-2 py-1 d-none d-md-inline" style="font-size: 0.75rem;">
                                    <i class="fas fa-check-circle text-success mr-1"></i> Sincronizado con SQL Server
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-info font-weight-bold mr-1" onclick="loadLiveContext()" title="Recargar datos en vivo desde la base de datos" style="border-radius: 8px;">
                                    <i class="fas fa-sync-alt mr-1"></i> Actualizar Contexto BD
                                </button>
                                <button class="btn btn-sm btn-outline-danger font-weight-bold" onclick="clearCopilotChat()" title="Limpiar conversación del chat" style="border-radius: 8px;">
                                    <i class="fas fa-trash-alt mr-1"></i> Reiniciar Chat
                                </button>
                            </div>
                        </div>

                        <div class="collapse mt-2" id="contextCollapse">
                            <div class="card card-body mb-0 p-3 border rounded-lg" style="background: #ffffff; border-color: #e2e8f0 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="font-weight-bold text-dark">
                                        <i class="fas fa-stream text-primary mr-1"></i> Instantánea inyectada al agente en cada consulta:
                                    </small>
                                    <small class="text-muted">Caja diaria · Stock crítico ≤ 3 · Ventas · Compras</small>
                                </div>
                                <textarea id="copilot-context-view" class="form-control font-monospace text-muted" rows="4" readonly style="font-size: 0.8rem; background: #f8fafc; border-radius: 8px;"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- CONTENEDOR PRINCIPAL DEL CHAT --}}
                    <div class="card border mb-3 shadow-none rounded-xl" style="border-radius: 16px; border-color: #e2e8f0 !important; overflow: hidden;">
                        
                        {{-- Ventana de mensajes --}}
                        <div id="chat-messages-container" class="card-body p-3 p-md-4" style="height: 520px; overflow-y: auto; background-color: #f8fafc;">
                            
                            {{-- Mensaje de bienvenida inicial --}}
                            <div class="d-flex mb-3">
                                <div class="mr-3 flex-shrink-0">
                                    <div class="rounded-circle p-1 shadow-sm bg-white" style="width: 44px; height: 44px; border: 2px solid #ec4899; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ asset('img/logo_eli_boutique.png') }}" alt="Eli Boutique" style="width: 32px; height: 32px; object-fit: contain;">
                                    </div>
                                </div>
                                <div class="bg-white p-3 p-md-4 rounded-xl shadow-sm border" style="max-width: 85%; border-radius: 16px; border-left: 3.5px solid #d4af37 !important; border-color: #e2e8f0;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="font-weight-bold mr-2" style="font-size: 0.92rem; color: #0f172a;">Eli Boutique AI</span>
                                            <span class="badge badge-light border text-primary" style="font-size: 0.68rem; font-weight: 700; padding: 2px 6px;">COPILOTO OFICIAL</span>
                                        </div>
                                        <small class="text-muted"><i class="far fa-clock mr-1"></i> En vivo</small>
                                    </div>
                                    <div class="text-dark" style="font-size: 0.94rem; line-height: 1.55; color: #1e293b;">
                                        ¡Hola! Soy tu <strong>Copiloto y Asesor de Modas de Eli Boutique</strong>. Tengo acceso directo y seguro a la base de datos de la empresa:
                                    </div>

                                    {{-- Mini Grid de capacidades en el saludo --}}
                                    <div class="row mt-3 g-2">
                                        <div class="col-md-4 mb-2 mb-md-0">
                                            <div class="p-2 px-3 rounded-lg border h-100" style="background: #faf5ff; border-color: #f3e8ff !important;">
                                                <div class="font-weight-bold" style="font-size: 0.8rem; color: #7e22ce;"><i class="fas fa-boxes mr-1"></i> Stock por Tallas</div>
                                                <small class="text-muted" style="font-size: 0.74rem;">Monitoreo de prendas críticas, variantes y quiebres de inventario.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2 mb-md-0">
                                            <div class="p-2 px-3 rounded-lg border h-100" style="background: #f0fdf4; border-color: #dcfce7 !important;">
                                                <div class="font-weight-bold" style="font-size: 0.8rem; color: #15803d;"><i class="fas fa-cash-register mr-1"></i> Caja & Ventas POS</div>
                                                <small class="text-muted" style="font-size: 0.74rem;">Cuadre de caja diaria, ingresos y reporte de comprobantes emitidos.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-2 px-3 rounded-lg border h-100" style="background: #eff6ff; border-color: #dbeafe !important;">
                                                <div class="font-weight-bold" style="font-size: 0.8rem; color: #1d4ed8;"><i class="fas fa-truck-loading mr-1"></i> Compras & Proveedores</div>
                                                <small class="text-muted" style="font-size: 0.74rem;">Órdenes de compra aprobadas, recepción y cuentas por pagar.</small>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Sugerencias Rápidas en Carrusel de Pills --}}
                        <div class="px-3 py-2 bg-white border-top d-flex align-items-center" style="border-color: #f1f5f9 !important; overflow-x: auto; white-space: nowrap; gap: 8px;">
                            <span class="text-muted font-weight-bold mr-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-bolt text-warning mr-1"></i> Consultas rápidas:
                            </span>
                            <button class="btn btn-sm eli-quick-chip" onclick="useQuickPrompt('¿Cuál es la prenda con más stock disponible hoy?')">
                                👗 Prenda con más stock
                            </button>
                            <button class="btn btn-sm eli-quick-chip" onclick="useQuickPrompt('¿Cuánto se ha vendido hoy y cómo está el estado de la caja diaria?')">
                                💰 Ventas y Caja de hoy
                            </button>
                            <button class="btn btn-sm eli-quick-chip" onclick="useQuickPrompt('¿Qué productos tienen stock crítico menor o igual a 3 unidades por talla?')">
                                ⚠️ Stock crítico por talla
                            </button>
                            <button class="btn btn-sm eli-quick-chip" onclick="useQuickPrompt('¿Cuáles son las últimas compras a proveedores registradas?')">
                                📦 Compras recientes
                            </button>
                            <button class="btn btn-sm eli-quick-chip" onclick="useQuickPrompt('¿Cuáles son los 5 productos más vendidos del catálogo?')">
                                🏆 Top 5 más vendidos
                            </button>
                        </div>

                        {{-- Input Footer Moderno con Voz y Envío --}}
                        <div class="card-footer bg-white border-top p-3" style="border-color: #f1f5f9 !important;">
                            <form id="copilot-form" onsubmit="handleCopilotSubmit(event)" class="m-0">
                                <div class="eli-input-wrapper d-flex align-items-center p-1 rounded-pill border" style="background: #ffffff; border-color: #cbd5e1 !important; box-shadow: 0 2px 6px rgba(0,0,0,0.04); transition: border-color 0.2s, box-shadow 0.2s;">
                                    
                                    {{-- Botón Micrófono --}}
                                    <button type="button" 
                                            id="btn-mic-copilot" 
                                            class="btn rounded-circle d-flex align-items-center justify-content-center mr-2 ml-1" 
                                            onclick="toggleVoiceInput('copilot-input', 'btn-mic-copilot')" 
                                            style="width: 40px; height: 40px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; transition: all 0.2s;"
                                            title="Dictar por voz (Español Perú)">
                                        <i class="fas fa-microphone" style="font-size: 0.95rem;"></i>
                                    </button>

                                    {{-- Input Text --}}
                                    <input type="text" 
                                           id="copilot-input" 
                                           class="form-control border-0 shadow-none px-2" 
                                           placeholder="Escribe tu consulta sobre prendas, tallas, ventas, compras o caja diaria..." 
                                           autocomplete="off" 
                                           required
                                           style="font-size: 0.92rem; background: transparent;">

                                    {{-- Botón Enviar --}}
                                    <button type="submit" 
                                            id="btn-send-copilot" 
                                            class="btn rounded-pill font-weight-bold px-4 py-2 ml-2 d-flex align-items-center" 
                                            style="background: linear-gradient(135deg, #0f172a, #2563eb); color: white; border: none; box-shadow: 0 4px 12px rgba(37,99,235,0.25); transition: all 0.25s;">
                                        <span class="mr-1 d-none d-sm-inline">Consultar</span>
                                        <i class="fas fa-paper-plane" style="font-size: 0.85rem;"></i>
                                    </button>
                                </div>
                                <div class="d-flex justify-content-between align-items-center px-3 pt-2">
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        <i class="fas fa-shield-alt text-success mr-1"></i> Consultas analíticas seguras de solo lectura (SQL Server)
                                    </small>
                                    <small class="text-muted d-none d-md-inline" style="font-size: 0.72rem;">
                                        Presiona <kbd style="background: #e2e8f0; color: #334155; padding: 1px 4px; border-radius: 4px;">Enter</kbd> para enviar
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: INSPECTOR RAG --}}
                <div class="tab-pane fade" id="tab-rag" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            
                            {{-- Card 1: Contexto --}}
                            <div class="card shadow-sm border rounded-xl mb-3" style="border-radius: 14px; border-color: #e2e8f0 !important;">
                                <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                                    <h5 class="m-0 font-weight-bold text-dark" style="font-size: 0.95rem;">
                                        <i class="fas fa-database text-info mr-2"></i> 1. Contexto de Datos (ERP / Datos Estrictos)
                                    </h5>
                                    <div class="dropdown">
                                        <button class="btn btn-xs btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" style="border-radius: 6px;">
                                            Plantillas de prueba
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="loadRagTemplate('inventario')">📦 Stock Crítico por Talla</a>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="loadRagTemplate('caja')">💰 Cuadre de Caja Diaria</a>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="loadRagTemplate('ventas')">📈 Ventas Recientes</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <p class="text-muted mb-2" style="font-size: 0.82rem;">
                                        Los datos ingresados aquí se inyectan directamente para un análisis estricto y sin alucinaciones:
                                    </p>
                                    <textarea id="rag-context-input" 
                                              class="form-control font-monospace p-3" 
                                              rows="8" 
                                              style="font-size: 0.82rem; background: #f8fafc; border-radius: 10px; border-color: #cbd5e1;" 
                                              placeholder="Ingresa datos de lotes, tablas o registros...">Catálogo de Muestra de Eli Boutique:
- Producto P001: Vestido Gala Elegante, Talla M: 4 unidades, Talla L: 1 unidad. Precio: S/ 150.00.
- Producto P002: Blusa Seda Floreada, Talla S: 0 unidades (Agotado), Talla M: 8 unidades. Precio: S/ 65.00.
- Producto P003: Pantalón Palazzo Denim, Talla 28: 12 unidades, Talla 30: 5 unidades. Precio: S/ 89.00.
- Caja CAJ001 hoy: Abierta. Total ingresos ventas: S/ 540.00. Total compras: S/ 0.00.</textarea>
                                </div>
                            </div>

                            {{-- Card 2: Pregunta --}}
                            <div class="card shadow-sm border rounded-xl" style="border-radius: 14px; border-color: #e2e8f0 !important;">
                                <div class="card-header py-3 bg-white border-bottom">
                                    <h5 class="m-0 font-weight-bold text-dark" style="font-size: 0.95rem;">
                                        <i class="fas fa-question-circle text-primary mr-2"></i> 2. Pregunta sobre el Contexto
                                    </h5>
                                </div>
                                <div class="card-body p-3">
                                    <div class="input-group">
                                        <input type="text" 
                                               id="rag-question-input" 
                                               class="form-control" 
                                               value="¿Qué prendas tienen stock menor a 2 unidades y cuál está completamente agotada?" 
                                               placeholder="Escribe tu pregunta sobre los datos de arriba..."
                                               style="border-radius: 8px 0 0 8px;">
                                        <div class="input-group-append">
                                            <button class="btn font-weight-bold px-3" 
                                                    id="btn-send-rag" 
                                                    onclick="handleRagSubmit()" 
                                                    style="background: #2563eb; color: white; border-radius: 0 8px 8px 0;">
                                                <i class="fas fa-search mr-1"></i> Analizar RAG
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card 3: Respuesta --}}
                        <div class="col-lg-6">
                            <div class="card shadow-sm border rounded-xl h-100" style="border-radius: 14px; border-color: #e2e8f0 !important;">
                                <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                                    <h5 class="m-0 font-weight-bold text-success" style="font-size: 0.95rem;">
                                        <i class="fas fa-check-circle mr-2"></i> Respuesta Verificada (RAG Estricto)
                                    </h5>
                                    <div>
                                        <button class="btn btn-xs btn-outline-secondary font-weight-bold mr-1" onclick="copyText('rag-response-content')" style="border-radius: 6px;">
                                            <i class="far fa-copy mr-1"></i> Copiar
                                        </button>
                                        <button class="btn btn-xs btn-outline-secondary font-weight-bold" onclick="speakText('rag-response-content')" style="border-radius: 6px;" title="Leer respuesta">
                                            <i class="fas fa-volume-up"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-4" id="rag-response-content" style="min-height: 380px; overflow-y: auto; background: #ffffff; font-size: 0.92rem; line-height: 1.6;">
                                    <div class="text-center text-muted py-5">
                                        <i class="fas fa-file-alt fa-3x mb-3 text-light" style="color: #cbd5e1 !important;"></i>
                                        <p class="mb-0">Ingresa datos y haz clic en <strong>Analizar RAG</strong> para ver la respuesta fundamentada.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: CONSULTA LIBRE (MODAS Y TENDENCIAS) --}}
                <div class="tab-pane fade" id="tab-libre" role="tabpanel">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            
                            {{-- Tarjetas de Inspiración Rápida --}}
                            <div class="row g-2 mb-3">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <div class="p-3 bg-white rounded-xl border shadow-sm h-100 eli-idea-card" 
                                         onclick="useLibrePrompt('¿Cuáles son las tendencias de moda femenina para la temporada actual en Perú y qué colores destacan?')"
                                         style="cursor: pointer; border-radius: 12px; transition: all 0.2s;">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="mr-2" style="font-size: 1.2rem;">🌸</span>
                                            <strong style="font-size: 0.88rem; color: #be185d;">Tendencias Temporada</strong>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.78rem;">Paletas de color y estilos en tendencia en Lima y provincias.</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <div class="p-3 bg-white rounded-xl border shadow-sm h-100 eli-idea-card" 
                                         onclick="useLibrePrompt('Sugiere 3 combinaciones de outfits ejecutivos usando pantalones palazzo y blusas de seda con accesorios.')"
                                         style="cursor: pointer; border-radius: 12px; transition: all 0.2s;">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="mr-2" style="font-size: 1.2rem;">✨</span>
                                            <strong style="font-size: 0.88rem; color: #b45309;">Combinación de Outfits</strong>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.78rem;">Propuestas de looks para asesorar a clientas en tienda.</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded-xl border shadow-sm h-100 eli-idea-card" 
                                         onclick="useLibrePrompt('¿Cuáles son las mejores recomendaciones de cuidado y lavado para prendas de lino, gasa y denim para instruir a nuestras clientas?')"
                                         style="cursor: pointer; border-radius: 12px; transition: all 0.2s;">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="mr-2" style="font-size: 1.2rem;">🧺</span>
                                            <strong style="font-size: 0.88rem; color: #0284c7;">Cuidado de Prendas</strong>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.78rem;">Tips de lavado y conservación de telas finas.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm border rounded-xl" style="border-radius: 14px; border-color: #e2e8f0 !important;">
                                <div class="card-header py-3 bg-white border-bottom">
                                    <h5 class="m-0 font-weight-bold text-dark" style="font-size: 0.95rem;">
                                        <i class="fas fa-globe text-success mr-2"></i> Consulta General al Motor de DeepSeek
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <p class="text-muted" style="font-size: 0.85rem;">
                                        Ideal para consultas de moda global, copywriting para redes sociales de la boutique, consejos de combinación y estrategias comerciales:
                                    </p>
                                    
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark" style="font-size: 0.88rem;">Pregunta a DeepSeek:</label>
                                        <textarea id="libre-question-input" 
                                                  class="form-control p-3" 
                                                  rows="3" 
                                                  style="border-radius: 10px; font-size: 0.92rem; border-color: #cbd5e1;"
                                                  placeholder="Ejemplo: Redacta 3 copys atractivos para Instagram anunciando una nueva colección de vestidos de fiesta en Eli Boutique..."></textarea>
                                    </div>

                                    <button class="btn font-weight-bold px-4 py-2" 
                                            id="btn-send-libre" 
                                            onclick="handleLibreSubmit()" 
                                            style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(16,185,129,0.25);">
                                        <i class="fas fa-paper-plane mr-1"></i> Consultar a DeepSeek
                                    </button>

                                    <hr class="my-4" style="border-color: #f1f5f9;">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="font-weight-bold mb-0 text-dark" style="font-size: 0.88rem;">Respuesta del Asistente:</label>
                                        <div>
                                            <button class="btn btn-xs btn-outline-secondary font-weight-bold mr-1" onclick="copyText('libre-response-content')" style="border-radius: 6px;">
                                                <i class="far fa-copy mr-1"></i> Copiar
                                            </button>
                                            <button class="btn btn-xs btn-outline-secondary font-weight-bold" onclick="speakText('libre-response-content')" style="border-radius: 6px;" title="Leer respuesta">
                                                <i class="fas fa-volume-up"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-xl border bg-white" 
                                         id="libre-response-content" 
                                         style="min-height: 200px; border-radius: 12px; border-color: #e2e8f0 !important; font-size: 0.92rem; line-height: 1.6;">
                                        <span class="text-muted font-italic">La respuesta libre del modelo aparecerá aquí...</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* Tipografía y acabados boutique */
    .eli-nav-pills .nav-link {
        color: #475569;
        border-radius: 10px;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    .eli-nav-pills .nav-link:hover {
        background-color: #f8fafc;
        color: #0f172a;
    }
    .eli-nav-pills .nav-link.active {
        background: linear-gradient(135deg, #0f172a, #1e293b) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25);
    }
    .eli-quick-chip {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.78rem;
        color: #2563eb;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .eli-quick-chip:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(37,99,235,0.12);
    }
    .eli-input-wrapper:focus-within {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.15) !important;
    }
    .eli-idea-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.08) !important;
        border-color: #cbd5e1 !important;
    }

    /* Burbujas del chat */
    .chat-bubble-user {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #ffffff;
        border-radius: 16px 16px 2px 16px;
    }
    .chat-bubble-bot {
        background: #ffffff;
        color: #0f172a;
        border-radius: 16px 16px 16px 2px;
        border: 1px solid #e2e8f0;
        border-left: 3.5px solid #d4af37 !important;
    }

    /* Estilos para renderizado de tablas Markdown en el chat */
    .ai-markdown-table {
        width: 100%;
        margin: 12px 0;
        font-size: 0.82rem;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        border-collapse: separate;
        border-spacing: 0;
    }
    .ai-markdown-table th {
        background: #0f172a;
        color: #ffffff;
        padding: 9px 12px;
        font-weight: 700;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #d4af37;
    }
    .ai-markdown-table td {
        padding: 8px 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .ai-markdown-table tr:nth-child(even) td {
        background-color: #f8fafc;
    }
    .ai-markdown-table tr:hover td {
        background-color: #fefce8;
    }

    /* Scrollbar personalizada para el chat */
    #chat-messages-container::-webkit-scrollbar {
        width: 6px;
    }
    #chat-messages-container::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    #chat-messages-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    #chat-messages-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@stop

@section('js')
<script>
    const CSRF_TOKEN = "{{ csrf_token() }}";
    let isRecording = false;
    let recognition = null;

    // 1. Verificar Estado del Backend y Latencia
    async function checkBackendHealth() {
        const badge = document.getElementById('ai-status-badge');
        badge.className = 'badge badge-warning px-2 py-1';
        badge.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Verificando';

        try {
            const start = Date.now();
            const res = await fetch("{{ route('ai.ping') }}");
            const data = await res.json();
            const latency = Date.now() - start;

            if (data.status === 'online' || data.status === 'ok' || data.ok === true) {
                badge.className = 'badge badge-success px-2 py-1';
                badge.innerHTML = `<i class="fas fa-check-circle mr-1"></i> Online (${latency}ms)`;
            } else {
                badge.className = 'badge badge-danger px-2 py-1';
                badge.innerHTML = `<i class="fas fa-times-circle mr-1"></i> ${data.message || 'Desconectado'}`;
            }
        } catch (e) {
            badge.className = 'badge badge-danger px-2 py-1';
            badge.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Error Red';
        }
    }

    // 2. Cargar Contexto RAG en Vivo
    async function loadLiveContext() {
        const area = document.getElementById('copilot-context-view');
        if (!area) return;
        area.value = "Sincronizando contexto con la base de datos de Eli Boutique...";

        try {
            const res = await fetch("{{ route('ai.contexto') }}");
            const data = await res.json();
            if (data.contexto) {
                area.value = data.contexto;
            } else {
                area.value = "No se pudo recuperar el contexto de negocio actual.";
            }
        } catch (e) {
            area.value = "Error al conectar con el endpoint de contexto en vivo.";
        }
    }

    // 3. Parser Markdown Liviano a HTML
    function renderMarkdownToHtml(md) {
        if (!md) return '';
        let html = md;

        // Escapar etiquetas peligrosas pero permitir formato
        html = html.replace(/</g, '&lt;').replace(/>/g, '&gt;');

        // Tablas Markdown
        const tableRegex = /((?:\|[^\n]+\|\r?\n)+)/g;
        html = html.replace(tableRegex, (match) => {
            return formatMarkdownTable(match);
        });

        // Encabezados
        html = html.replace(/^### (.*$)/gim, '<h6 class="font-weight-bold text-dark mt-3 mb-2" style="color:#0f172a !important;">$1</h6>');
        html = html.replace(/^## (.*$)/gim, '<h5 class="font-weight-bold text-dark mt-3 mb-2" style="color:#0f172a !important; border-bottom: 1.5px solid #e2e8f0; padding-bottom: 4px;">$1</h5>');
        html = html.replace(/^# (.*$)/gim, '<h4 class="font-weight-bold text-dark mt-3 mb-2" style="color:#0f172a !important;">$1</h4>');

        // Negrita y cursiva
        html = html.replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>');
        html = html.replace(/\*(.*?)\*/gim, '<em>$1</em>');

        // Código inline y bloques
        html = html.replace(/`([^`]+)`/g, '<code class="bg-light text-danger px-1 py-0 rounded" style="font-size:0.86rem;">$1</code>');

        // Listas desordenadas
        html = html.replace(/^\s*[-*]\s+(.*)$/gim, '<li class="mb-1">$1</li>');
        html = html.replace(/(<li.*<\/li>)/s, '<ul class="pl-3 mb-2">$1</ul>');

        // Saltos de línea dobles a párrafos
        html = html.replace(/\n\n+/g, '<br><br>');

        return html;
    }

    function formatMarkdownTable(raw) {
        const lines = raw.trim().split('\n').map(l => l.trim()).filter(l => l.length > 0);
        if (lines.length < 2) return raw;

        const parseRow = (line) => line.split('|').map(c => c.trim()).filter((c, idx, arr) => idx > 0 && idx < arr.length - 1);

        const headers = parseRow(lines[0]);
        // Ignorar línea divisoria (línea 1)
        const rows = lines.slice(2).map(parseRow);

        let t = '<div class="table-responsive my-2"><table class="ai-markdown-table shadow-sm"><thead><tr>';
        headers.forEach(h => {
            t += `<th>${escapeHtml(h)}</th>`;
        });
        t += '</tr></thead><tbody>';
        rows.forEach(r => {
            t += '<tr>';
            r.forEach(c => {
                t += `<td>${escapeHtml(c)}</td>`;
            });
            t += '</tr>';
        });
        t += '</tbody></table></div>';
        return t;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }

    // 4. Copilot Submit
    async function handleCopilotSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('copilot-input');
        const query = input.value.trim();
        if (!query) return;

        stopVoiceRecording();
        appendUserMessage(query);
        input.value = '';

        const loadingId = 'loading-' + Date.now();
        appendBotLoading(loadingId);

        const btnSend = document.getElementById('btn-send-copilot');
        btnSend.disabled = true;

        try {
            const res = await fetch("{{ route('ai.consultar') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    pregunta: query,
                    modulo: 'General',
                    route: window.location.pathname
                })
            });

            const data = await res.json();
            removeLoadingBubble(loadingId);

            if (data.respuesta) {
                appendBotMessage(data.respuesta, data.tools, data.durationMs, data.totalTokens);
            } else if (data.error) {
                appendBotMessage('⚠️ Error: ' + data.error);
            } else {
                appendBotMessage('No se pudo obtener una respuesta del asistente.');
            }
        } catch (err) {
            removeLoadingBubble(loadingId);
            appendBotMessage('⚠️ Error de conexión con el servidor. Inténtalo de nuevo.');
        } finally {
            btnSend.disabled = false;
        }
    }

    function appendUserMessage(text) {
        const container = document.getElementById('chat-messages-container');
        const bubble = document.createElement('div');
        bubble.className = 'd-flex justify-content-end mb-3';
        bubble.innerHTML = `
            <div class="chat-bubble-user p-3 shadow-sm" style="max-width: 80%;">
                <div style="font-size: 0.93rem; line-height: 1.5;">${escapeHtml(text)}</div>
                <div class="text-right mt-1" style="font-size: 0.72rem; color: #94a3b8;">
                    <i class="fas fa-check-double mr-1" style="color: #38bdf8;"></i> Tú
                </div>
            </div>
            <div class="ml-2 flex-shrink-0">
                <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; border: 1.5px solid #475569;">
                    <i class="fas fa-user" style="font-size: 0.9rem;"></i>
                </div>
            </div>
        `;
        container.appendChild(bubble);
        container.scrollTop = container.scrollHeight;
    }

    function appendBotLoading(id) {
        const container = document.getElementById('chat-messages-container');
        const bubble = document.createElement('div');
        bubble.id = id;
        bubble.className = 'd-flex mb-3';
        bubble.innerHTML = `
            <div class="mr-3 flex-shrink-0">
                <div class="rounded-circle p-1 shadow-sm bg-white" style="width: 42px; height: 42px; border: 2px solid #ec4899; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('img/logo_eli_boutique.png') }}" alt="Eli Boutique" style="width: 30px; height: 30px; object-fit: contain;">
                </div>
            </div>
            <div class="chat-bubble-bot p-3 shadow-sm" style="max-width: 85%;">
                <div class="d-flex align-items-center text-muted" style="font-size: 0.88rem;">
                    <i class="fas fa-spinner fa-spin mr-2 text-primary"></i> Analizando datos de Eli Boutique con DeepSeek...
                </div>
            </div>
        `;
        container.appendChild(bubble);
        container.scrollTop = container.scrollHeight;
    }

    function removeLoadingBubble(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function appendBotMessage(markdownText, tools = [], durationMs = null, tokens = null) {
        const container = document.getElementById('chat-messages-container');
        const bubble = document.createElement('div');
        bubble.className = 'd-flex mb-3';
        const msgId = 'msg-' + Date.now();

        let toolInfo = '';
        if (tools && tools.length > 0) {
            toolInfo = `<div class="mt-2 pt-2 border-top text-muted small" style="font-size: 0.76rem; border-color: #f1f5f9 !important;">
                <span class="badge badge-light border text-info px-2 py-1 mb-1 font-weight-bold">
                    <i class="fas fa-database mr-1"></i> Consultas SQL Ejecutadas (${tools.length})
                </span>
                <ul class="mb-0 pl-3 mt-1">
                    ${tools.map(t => `<li><code class="text-dark bg-light px-1">${escapeHtml(t.queryOrInput || '')}</code> <span class="badge badge-light">(${t.rowCount} filas en ${t.executionTimeMs}ms)</span></li>`).join('')}
                </ul>
            </div>`;
        }

        let metaInfo = '';
        if (durationMs || tokens) {
            metaInfo = `<div class="text-muted mt-2 pt-1 border-top d-flex justify-content-between align-items-center" style="font-size: 0.72rem; border-color: #f1f5f9 !important;">
                <span>
                    <i class="fas fa-bolt text-warning mr-1"></i> ${durationMs ? durationMs + 'ms' : ''} 
                    ${tokens ? '· ' + tokens + ' tokens' : ''}
                </span>
                <div>
                    <button class="btn btn-xs btn-link text-muted p-0 mr-2" onclick="speakText('${msgId}')" title="Leer en voz alta">
                        <i class="fas fa-volume-up"></i>
                    </button>
                    <button class="btn btn-xs btn-link text-muted p-0" onclick="copyCopilotMsg('${msgId}')" title="Copiar texto">
                        <i class="far fa-copy"></i>
                    </button>
                </div>
            </div>`;
        }

        bubble.innerHTML = `
            <div class="mr-3 flex-shrink-0">
                <div class="rounded-circle p-1 shadow-sm bg-white" style="width: 42px; height: 42px; border: 2px solid #ec4899; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('img/logo_eli_boutique.png') }}" alt="Eli Boutique" style="width: 30px; height: 30px; object-fit: contain;">
                </div>
            </div>
            <div class="chat-bubble-bot p-3 p-md-4 shadow-sm" style="max-width: 85%;">
                <div class="font-weight-bold mb-2 d-flex justify-content-between align-items-center" style="font-size: 0.88rem; color: #0f172a;">
                    <span>Eli Boutique AI</span>
                    <span class="badge badge-light border text-muted" style="font-size: 0.68rem;">Análisis Confirmado</span>
                </div>
                <div id="${msgId}" class="text-dark" style="font-size: 0.93rem; line-height: 1.6;">
                    ${renderMarkdownToHtml(markdownText)}
                </div>
                ${toolInfo}
                ${metaInfo}
            </div>
        `;
        container.appendChild(bubble);
        container.scrollTop = container.scrollHeight;
    }

    function useQuickPrompt(text) {
        document.getElementById('copilot-input').value = text;
        document.getElementById('copilot-form').dispatchEvent(new Event('submit'));
    }

    function useLibrePrompt(text) {
        document.getElementById('libre-question-input').value = text;
        handleLibreSubmit();
    }

    function clearCopilotChat() {
        const container = document.getElementById('chat-messages-container');
        container.innerHTML = `
            <div class="d-flex mb-3">
                <div class="mr-3 flex-shrink-0">
                    <div class="rounded-circle p-1 shadow-sm bg-white" style="width: 42px; height: 42px; border: 2px solid #ec4899; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('img/logo_eli_boutique.png') }}" alt="Eli Boutique" style="width: 30px; height: 30px; object-fit: contain;">
                    </div>
                </div>
                <div class="chat-bubble-bot p-3 shadow-sm" style="max-width: 85%;">
                    <div class="font-weight-bold mb-1" style="font-size: 0.88rem; color: #0f172a;">Eli Boutique AI</div>
                    <div class="text-dark" style="font-size: 0.92rem; line-height: 1.5;">
                        Historial reiniciado. ¿En qué otro análisis operacional o consulta de prendas te puedo asistir hoy?
                    </div>
                </div>
            </div>
        `;
    }

    // 5. Speech-to-Text
    function toggleVoiceInput(inputId, buttonId) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            alert('Tu navegador no soporta reconocimiento de voz. Usa Google Chrome o Microsoft Edge.');
            return;
        }

        const btn = document.getElementById(buttonId);
        const input = document.getElementById(inputId);

        if (isRecording) {
            stopVoiceRecording();
            return;
        }

        recognition = new SpeechRecognition();
        recognition.lang = 'es-PE';
        recognition.continuous = false;
        recognition.interimResults = false;

        recognition.onstart = () => {
            isRecording = true;
            btn.style.background = '#ef4444';
            btn.style.color = '#ffffff';
            btn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
        };

        recognition.onresult = (e) => {
            let transcript = e.results[0][0].transcript;
            transcript = transcript.replace(/\b(este|eh+|em+|o sea|a ver|bueno|tipo|digamos)\b/gi, '').trim();
            if (transcript.length > 0) {
                transcript = transcript.charAt(0).toUpperCase() + transcript.slice(1);
            }
            input.value = transcript;
        };

        recognition.onerror = () => {
            stopVoiceRecording();
        };

        recognition.onend = () => {
            stopVoiceRecording();
        };

        recognition.start();
    }

    function stopVoiceRecording() {
        if (recognition && isRecording) {
            try { recognition.stop(); } catch(e) {}
        }
        isRecording = false;
        const btn = document.getElementById('btn-mic-copilot');
        if (btn) {
            btn.style.background = '#f1f5f9';
            btn.style.color = '#475569';
            btn.innerHTML = '<i class="fas fa-microphone"></i>';
        }
    }

    // 6. Text-to-Speech
    function speakText(elementId) {
        if (!('speechSynthesis' in window)) {
            alert('Tu navegador no soporta lectura de voz.');
            return;
        }

        window.speechSynthesis.cancel();
        const el = document.getElementById(elementId);
        if (!el) return;

        let cleanText = el.innerText.replace(/\|/g, ' ').replace(/⚠️/g, 'Alerta: ').replace(/[-*•]/g, '');
        const utterance = new SpeechSynthesisUtterance(cleanText);
        utterance.lang = 'es-PE';
        utterance.rate = 1.05;
        window.speechSynthesis.speak(utterance);
    }

    function copyCopilotMsg(elementId) {
        const el = document.getElementById(elementId);
        if (el) {
            navigator.clipboard.writeText(el.innerText);
            alert('Texto copiado al portapapeles.');
        }
    }

    function copyText(containerId) {
        const el = document.getElementById(containerId);
        if (el) {
            navigator.clipboard.writeText(el.innerText);
            alert('Copiado al portapapeles.');
        }
    }

    // 7. Plantillas RAG
    function loadRagTemplate(type) {
        const area = document.getElementById('rag-context-input');
        const q = document.getElementById('rag-question-input');
        if (type === 'inventario') {
            area.value = "Inventario Crítico de Eli Boutique:\n- Polo Casual Algodón (POL-001), Talla M: 1 unidad (Crítico)\n- Shorts Denim (SHO-041), Talla 32: 1 unidad (Crítico)\n- Leggings Deportivos (LEG-082), Talla S: 0 unidades (Agotado)\n- Jeans Rectos (JEA-022), Talla 32: 2 unidades (Bajo stock)";
            q.value = "¿Cuáles son las prendas agotadas y cuáles tienen solo 1 unidad?";
        } else if (type === 'caja') {
            area.value = "Caja Diaria Eli Boutique:\n- Caja N° 1 (Turno Mañana): Saldo inicial: S/ 150.00\n- Total Ventas Efectivo: S/ 450.00\n- Total Ventas Yape/Plin: S/ 320.00\n- Gastos menores en efectivo: S/ 35.00\n- Estado: Abierta";
            q.value = "¿Cuánto dinero en efectivo físico debe haber en caja según el cuadre?";
        } else if (type === 'ventas') {
            area.value = "Ventas Registradas Hoy:\n- Boleta B001-104: 2x Polo Casual Algodón, Total: S/ 40.00\n- Boleta B001-105: 1x Abrigo Trench Talla L, Total: S/ 120.00\n- Boleta B001-106: 1x Pantalón de Vestir Talla 28, Total: S/ 60.00";
            q.value = "¿Cuál es el ticket promedio de las ventas de hoy y cuál fue la venta más alta?";
        }
    }

    // 8. Inspector RAG Submit
    async function handleRagSubmit() {
        const context = document.getElementById('rag-context-input').value.trim();
        const question = document.getElementById('rag-question-input').value.trim();
        const resBox = document.getElementById('rag-response-content');
        const btn = document.getElementById('btn-send-rag');

        if (!question || !context) {
            alert('Por favor ingresa tanto el contexto como la pregunta.');
            return;
        }

        btn.disabled = true;
        resBox.innerHTML = '<div class="text-muted text-center py-4"><i class="fas fa-spinner fa-spin mr-1"></i> Analizando contexto con DeepSeek...</div>';

        try {
            const res = await fetch("{{ route('ai.consultarConContexto') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ pregunta: question, contexto: context })
            });

            const data = await res.json();
            if (data.respuesta) {
                resBox.innerHTML = renderMarkdownToHtml(data.respuesta);
            } else {
                resBox.innerHTML = '<div class="text-danger">Error: ' + (data.error || 'No se obtuvo respuesta.') + '</div>';
            }
        } catch (e) {
            resBox.innerHTML = '<div class="text-danger">Error de conexión al procesar RAG.</div>';
        } finally {
            btn.disabled = false;
        }
    }

    // 9. Consulta Libre Submit
    async function handleLibreSubmit() {
        const question = document.getElementById('libre-question-input').value.trim();
        const resBox = document.getElementById('libre-response-content');
        const btn = document.getElementById('btn-send-libre');

        if (!question) {
            alert('Por favor escribe tu consulta.');
            return;
        }

        btn.disabled = true;
        resBox.innerHTML = '<div class="text-muted text-center py-4"><i class="fas fa-spinner fa-spin mr-1"></i> Consultando a DeepSeek...</div>';

        try {
            const res = await fetch("{{ route('ai.consultarConContexto') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ pregunta: question, contexto: '' })
            });

            const data = await res.json();
            if (data.respuesta) {
                resBox.innerHTML = renderMarkdownToHtml(data.respuesta);
            } else {
                resBox.innerHTML = '<div class="text-danger">Error: ' + (data.error || 'No se obtuvo respuesta.') + '</div>';
            }
        } catch (e) {
            resBox.innerHTML = '<div class="text-danger">Error de conexión con el motor DeepSeek.</div>';
        } finally {
            btn.disabled = false;
        }
    }

    // Inicialización al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        checkBackendHealth();
        loadLiveContext();
    });
</script>
@stop
