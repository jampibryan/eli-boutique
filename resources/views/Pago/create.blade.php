@extends('adminlte::page')

@section('title', 'Pago')

@section('content_header')
@stop

@section('css')
<style>
    .payment-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 30px;
        margin-bottom: 20px;
    }
    
    .transaction-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;
    }
    
    .transaction-info h3 {
        margin: 0 0 10px 0;
        font-size: 24px;
        font-weight: bold;
    }
    
    .transaction-info .amount {
        font-size: 36px;
        font-weight: bold;
        margin: 10px 0;
    }
    
    .form-group label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .form-control {
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        padding: 12px 15px;
        font-size: 16px;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .amount-display {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .amount-display .label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    
    .amount-display .value {
        font-size: 28px;
        font-weight: bold;
        color: #333;
    }
    
    .vuelto-display {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        margin-top: 20px;
    }
    
    .vuelto-display.negative {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    }
    
    .vuelto-display .label {
        font-size: 14px;
        margin-bottom: 5px;
        opacity: 0.9;
    }
    
    .vuelto-display .value {
        font-size: 32px;
        font-weight: bold;
    }
    
    .btn-registrar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 15px 40px;
        font-size: 18px;
        font-weight: bold;
        border-radius: 10px;
        width: 100%;
        margin-top: 20px;
        transition: all 0.3s;
    }
    
    .btn-registrar:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-registrar:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-back {
        background: white;
        border: 2px solid #667eea;
        color: #667eea;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-back:hover {
        background: #667eea;
        color: white;
    }
    
    .comprobante-select {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 16px;
        height: auto;
        line-height: 1.5;
    }
    
    .input-group-text {
        background: #667eea;
        color: white;
        border: none;
        font-weight: bold;
        font-size: 18px;
        padding: 0 15px;
    }
</style>
@stop

@section('content')
    <a href="{{ $type === 'venta' ? route('ventas.edit', $transaction->id) : route($type.'s.index') }}" class="btn btn-back mb-3">
        <i class="fas fa-arrow-left"></i> Regresar
    </a>

    <div class="payment-card">
        <!-- Información de la transacción -->
        <div class="transaction-info">
            <h3>
                <i class="fas fa-{{ $type === 'venta' ? 'shopping-cart' : 'box' }}"></i>
                {{ $type === 'venta' ? 'Venta' : 'Compra' }} #{{ $transaction->{'codigo' . ucfirst($type)} ?? 'N/A' }}
            </h3>
            <div class="amount">
                S/ {{ number_format($transaction->montoTotal, 2) }}
            </div>
            <small><i class="fas fa-info-circle"></i> Precio incluye IGV</small>
        </div>

        <form action="{{ route('pagos.store', [$transaction->id, $type]) }}" method="POST" id="pagoForm">
            @csrf

            <hr style="margin: 30px 0; border: none; border-top: 2px solid #e0e0e0;">

            <!-- Comprobante -->
            <div class="form-group">
                <label for="comprobante_id">
                    <i class="fas fa-receipt"></i> Tipo de Comprobante
                </label>
                <select name="comprobante_id" id="comprobante_id" class="form-control comprobante-select" required>
                    <option value="">Seleccionar Comprobante</option>
                    @foreach($comprobantes as $comprobante)
                        <option value="{{ $comprobante->id }}">
                            {{ $comprobante->descripcionCOM }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($type === 'venta')
                <!-- Monto Total de la Venta -->
                <div class="amount-display">
                    <div class="label">Total a Cobrar</div>
                    <div class="value">S/ {{ number_format($transaction->montoTotal, 2) }}</div>
                </div>

                <!-- Importe Recibido -->
                <div class="form-group">
                    <label for="importe">
                        <i class="fas fa-money-bill-wave"></i> Importe Recibido del Cliente
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">S/</span>
                        </div>
                        <input type="number" 
                               class="form-control" 
                               name="importe" 
                               id="importe" 
                               step="0.01" 
                               min="0" 
                               placeholder="0.00"
                               required
                               autofocus>
                    </div>
                    <small class="text-muted">Ingrese el monto que entrega el cliente</small>
                </div>

                <!-- Vuelto -->
                <div class="vuelto-display" id="vueltoDisplay" style="display: none;">
                    <div class="label">
                        <i class="fas fa-hand-holding-usd"></i> VUELTO A ENTREGAR
                    </div>
                    <div class="value" id="vueltoValue">S/ 0.00</div>
                    <input type="hidden" name="vuelto" id="vuelto" value="0">
                </div>

                <div class="alert alert-warning" id="faltanteAlert" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Falta:</strong> <span id="faltanteValue">S/ 0.00</span>
                </div>
            @else
                <!-- Importe Recibido para Compra -->
                <div class="form-group">
                    <label for="importe">
                        <i class="fas fa-dollar-sign"></i> Monto del Pago
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">S/</span>
                        </div>
                        <input type="number" 
                               class="form-control" 
                               name="importe" 
                               id="importe" 
                               step="0.01" 
                               min="0" 
                               placeholder="0.00"
                               required
                               autofocus>
                    </div>
                </div>
            @endif

            <!-- Botón para registrar el pago -->
            <button type="submit" class="btn btn-registrar" id="btnRegistrar">
                <i class="fas fa-check-circle"></i> Registrar Pago
            </button>
        </form>
    </div>
@stop

@section('js')
    @if($type === 'venta')
        <script>
            const importeInput = document.getElementById('importe');
            const montoTotal = parseFloat({{ $transaction->montoTotal }});
            const vueltoDisplay = document.getElementById('vueltoDisplay');
            const vueltoValue = document.getElementById('vueltoValue');
            const vueltoInput = document.getElementById('vuelto');
            const faltanteAlert = document.getElementById('faltanteAlert');
            const faltanteValue = document.getElementById('faltanteValue');
            const btnRegistrar = document.getElementById('btnRegistrar');

            importeInput.addEventListener('input', function() {
                const importe = parseFloat(this.value) || 0;
                const diferencia = importe - montoTotal;

                if (importe > 0) {
                    if (diferencia >= 0) {
                        // Hay vuelto
                        vueltoDisplay.style.display = 'block';
                        vueltoDisplay.classList.remove('negative');
                        vueltoValue.textContent = 'S/ ' + diferencia.toFixed(2);
                        vueltoInput.value = diferencia.toFixed(2);
                        faltanteAlert.style.display = 'none';
                        btnRegistrar.disabled = false;
                    } else {
                        // Falta dinero
                        vueltoDisplay.style.display = 'none';
                        faltanteAlert.style.display = 'block';
                        faltanteValue.textContent = 'S/ ' + Math.abs(diferencia).toFixed(2);
                        vueltoInput.value = '0';
                        btnRegistrar.disabled = true;
                    }
                } else {
                    vueltoDisplay.style.display = 'none';
                    faltanteAlert.style.display = 'none';
                    vueltoInput.value = '0';
                    btnRegistrar.disabled = true;
                }
            });

            // Validar antes de enviar
            document.getElementById('pagoForm').addEventListener('submit', function(e) {
                const importe = parseFloat(importeInput.value) || 0;
                if (importe < montoTotal) {
                    e.preventDefault();
                    alert('El importe recibido no puede ser menor al total de la venta.');
                    importeInput.focus();
                }
            });
        </script>
    @endif
@stop
