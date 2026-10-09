{{-- BOTÓN NAVBAR MÓDULO ASISTENTE IA — ELI BOUTIQUE --}}
<li class="nav-item d-flex align-items-center mx-1" style="position: relative;">
    <a href="{{ url('asistente-ia') }}" 
       id="eli-ai-nav-trigger"
       class="nav-link p-0 d-flex align-items-center justify-content-center text-decoration-none position-relative"
       style="cursor: pointer; outline: none;"
       title="Ir al Módulo Asistente IA — Eli Boutique">
        
        <span class="eli-logo-nav-circle d-inline-flex align-items-center justify-content-center"
              style="width: 38px; height: 38px; border-radius: 50%; background: #ffffff; border: 2px solid #ec4899; box-shadow: 0 2px 8px rgba(236, 72, 153, 0.3); transition: all 0.25s ease; padding: 3px;">
            <img src="{{ asset('img/logo_eli_boutique.png') }}" 
                 alt="Eli Boutique IA" 
                 style="width: 100%; height: 100%; object-fit: contain; pointer-events: none;">
        </span>

        {{-- Insignia IA Online con pulso sutil --}}
        <span class="eli-logo-nav-badge" 
              style="position: absolute; top: -3px; right: -4px; background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; font-size: 9px; font-weight: 800; padding: 1px 4px; border-radius: 8px; border: 1.5px solid #ffffff; box-shadow: 0 1px 4px rgba(0,0,0,0.2); line-height: 1.1;">
            IA
        </span>
    </a>
</li>

<style>
    .eli-logo-nav-circle:hover {
        transform: scale(1.12);
        box-shadow: 0 0 14px rgba(236, 72, 153, 0.65) !important;
        border-color: #db2777 !important;
    }
    .eli-logo-nav-badge {
        animation: eliPulseBadge 2.2s infinite ease-in-out;
    }
    @keyframes eliPulseBadge {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            transform: scale(1.1);
            box-shadow: 0 0 0 5px rgba(16, 185, 129, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }
</style>
