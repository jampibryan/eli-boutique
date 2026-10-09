{{-- WIDGET FLOTANTE GLOBAL ASISTENTE IA ELI BOUTIQUE --}}
<div id="eli-ai-floating-container" style="position: fixed; bottom: 25px; right: 25px; z-index: 99999; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    {{-- BOTÓN FLOTANTE CIRCULAR DRAGGABLE CON LOGO OFICIAL --}}
    <div id="eli-ai-floating-btn" 
         title="Asistente Inteligente Eli Boutique (Arrastra para mover)"
         style="width: 60px; height: 60px; border-radius: 50%; background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.22); display: flex; align-items: center; justify-content: center; cursor: grab; user-select: none; border: 2.5px solid #2563eb; transition: transform 0.2s, box-shadow 0.2s; position: relative;">
        
        <img src="{{ asset('img/logo_eli_boutique.png') }}" alt="Eli Boutique" style="width: 38px; height: 38px; object-fit: contain; pointer-events: none;">
        
        {{-- Insignia IA --}}
        <span style="position: absolute; top: -3px; right: -3px; background: linear-gradient(135deg, #10b981, #059669); color: white; font-size: 10px; font-weight: 800; padding: 2px 5px; border-radius: 10px; border: 2px solid #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">
            IA
        </span>
    </div>

    {{-- MODAL SUPERPUESTO DEL CHATBOT --}}
    <div id="eli-ai-chat-window" style="display: none; width: 380px; max-width: calc(100vw - 30px); height: 530px; max-height: calc(100vh - 80px); background: #ffffff; border-radius: 16px; box-shadow: 0 10px 35px rgba(0,0,0,0.25); border: 1px solid #e2e8f0; flex-direction: column; overflow: hidden; position: absolute; bottom: 75px; right: 0;">
        
        {{-- Header del Modal --}}
        <div style="background: linear-gradient(135deg, #1e3a8a, #2563eb); color: white; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; border-top-left-radius: 16px; border-top-right-radius: 16px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="background: #ffffff; padding: 3px; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('img/logo_eli_boutique.png') }}" alt="Eli Boutique" style="width: 22px; height: 22px; object-fit: contain;">
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 14px; line-height: 1.2;">Eli Boutique AI</div>
                    <div style="font-size: 11px; opacity: 0.85;">Copiloto y Asesor de Modas</div>
                </div>
            </div>
            <div style="display: flex; gap: 6px;">
                <button type="button" onclick="toggleEliAiModal()" style="background: transparent; border: none; color: white; font-size: 15px; cursor: pointer; opacity: 0.8; padding: 2px 6px;" title="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Cuerpo de Mensajes --}}
        <div id="eli-ai-widget-messages" style="flex: 1; padding: 14px; overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 10px;">
            {{-- Mensaje de bienvenida inicial --}}
            <div style="display: flex; gap: 8px; align-items: flex-start;">
                <div style="background: #2563eb; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                    <i class="fas fa-robot"></i>
                </div>
                <div style="background: #ffffff; padding: 10px 12px; border-radius: 12px 12px 12px 2px; border: 1px solid #e2e8f0; font-size: 12.5px; color: #1e293b; line-height: 1.45; max-width: 85%; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    ¡Hola! Soy tu asistente de <strong>Eli Boutique</strong>. ¿Qué deseas consultar sobre el catálogo, inventario por talla, ventas o caja diaria?
                </div>
            </div>
        </div>

        {{-- Sugerencias Rápidas --}}
        <div style="padding: 6px 12px; background: #f1f5f9; border-top: 1px solid #e2e8f0; display: flex; gap: 6px; overflow-x: auto; white-space: nowrap;">
            <button type="button" onclick="sendWidgetQuickPrompt('¿Cuál es la prenda con más stock actualmente?')" style="background: white; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 8px; font-size: 11px; color: #2563eb; cursor: pointer; font-weight: 500;">
                👗 Prenda con más stock
            </button>
            <button type="button" onclick="sendWidgetQuickPrompt('¿Cómo está la caja diaria hoy y cuánto se ha vendido?')" style="background: white; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 8px; font-size: 11px; color: #2563eb; cursor: pointer; font-weight: 500;">
                💰 Caja y Ventas
            </button>
            <button type="button" onclick="sendWidgetQuickPrompt('¿Qué productos tienen menos de 3 unidades en stock por talla?')" style="background: white; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 8px; font-size: 11px; color: #2563eb; cursor: pointer; font-weight: 500;">
                ⚠️ Stock crítico
            </button>
        </div>

        {{-- Input Footer --}}
        <div style="padding: 10px 12px; background: #ffffff; border-top: 1px solid #e2e8f0;">
            <form id="eli-ai-widget-form" onsubmit="handleWidgetSubmit(event)" style="display: flex; gap: 6px; align-items: center; margin: 0;">
                <button type="button" id="eli-ai-widget-mic" onclick="toggleWidgetVoice()" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #64748b; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px;" title="Dictar por voz">
                    <i class="fas fa-microphone"></i>
                </button>
                <input type="text" id="eli-ai-widget-input" placeholder="Pregunta sobre prendas, ventas, caja..." autocomplete="off" required style="flex: 1; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; font-size: 12.5px; outline: none;">
                <button type="submit" id="eli-ai-widget-send" style="background: #2563eb; color: white; border: none; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const floatBtn = document.getElementById('eli-ai-floating-btn');
    const chatWin = document.getElementById('eli-ai-chat-window');
    const container = document.getElementById('eli-ai-floating-container');
    const messagesBox = document.getElementById('eli-ai-widget-messages');
    const input = document.getElementById('eli-ai-widget-input');
    const micBtn = document.getElementById('eli-ai-widget-mic');
    const sendBtn = document.getElementById('eli-ai-widget-send');

    let isDragging = false;
    let startX, startY, initialLeft, initialTop;
    let hasMoved = false;
    let widgetRecognition = null;
    let isWidgetRecording = false;

    // Abrir/Cerrar Modal
    window.toggleEliAiModal = function() {
        if (chatWin.style.display === 'none' || chatWin.style.display === '') {
            chatWin.style.display = 'flex';
            setTimeout(() => input.focus(), 100);
        } else {
            chatWin.style.display = 'none';
        }
    };

    // Arrastre Draggable para no tapar elementos
    floatBtn.addEventListener('mousedown', function(e) {
        if (e.button !== 0) return;
        isDragging = true;
        hasMoved = false;
        startX = e.clientX;
        startY = e.clientY;
        const rect = container.getBoundingClientRect();
        initialLeft = rect.left;
        initialTop = rect.top;
        floatBtn.style.cursor = 'grabbing';
    });

    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        const dx = e.clientX - startX;
        const dy = e.clientY - startY;
        if (Math.hypot(dx, dy) > 5) {
            hasMoved = true;
            container.style.left = (initialLeft + dx) + 'px';
            container.style.top = (initialTop + dy) + 'px';
            container.style.bottom = 'auto';
            container.style.right = 'auto';
        }
    });

    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            floatBtn.style.cursor = 'grab';
            if (!hasMoved) {
                toggleEliAiModal();
            }
        }
    });

    // Touch support (móviles / tablets)
    floatBtn.addEventListener('touchstart', function(e) {
        const touch = e.touches[0];
        isDragging = true;
        hasMoved = false;
        startX = touch.clientX;
        startY = touch.clientY;
        const rect = container.getBoundingClientRect();
        initialLeft = rect.left;
        initialTop = rect.top;
    }, {passive: true});

    document.addEventListener('touchmove', function(e) {
        if (!isDragging) return;
        const touch = e.touches[0];
        const dx = touch.clientX - startX;
        const dy = touch.clientY - startY;
        if (Math.hypot(dx, dy) > 5) {
            hasMoved = true;
            container.style.left = (initialLeft + dx) + 'px';
            container.style.top = (initialTop + dy) + 'px';
            container.style.bottom = 'auto';
            container.style.right = 'auto';
        }
    }, {passive: true});

    document.addEventListener('touchend', function() {
        if (isDragging) {
            isDragging = false;
            if (!hasMoved) {
                toggleEliAiModal();
            }
        }
    });

    // Envío de mensaje
    window.handleWidgetSubmit = async function(e) {
        e.preventDefault();
        const query = input.value.trim();
        if (!query) return;

        stopWidgetVoice();
        input.value = '';

        // Burbuja de usuario
        appendUserWidgetMsg(query);

        // Burbuja de carga
        const loadId = 'w-load-' + Date.now();
        appendBotWidgetLoading(loadId);
        sendBtn.disabled = true;

        try {
            const res = await fetch("{{ route('ai.consultar') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    pregunta: query,
                    modulo: 'Global',
                    route: window.location.pathname
                })
            });

            const data = await res.json();
            const loadingElem = document.getElementById(loadId);
            if (loadingElem) loadingElem.remove();

            if (data.respuesta) {
                appendBotWidgetMsg(data.respuesta);
            } else if (data.error) {
                appendBotWidgetMsg('⚠️ Error: ' + data.error);
            } else {
                appendBotWidgetMsg('No se pudo obtener respuesta.');
            }
        } catch (err) {
            const loadingElem = document.getElementById(loadId);
            if (loadingElem) loadingElem.remove();
            appendBotWidgetMsg('⚠️ Error de conexión.');
        } finally {
            sendBtn.disabled = false;
        }
    };

    window.sendWidgetQuickPrompt = function(promptText) {
        input.value = promptText;
        document.getElementById('eli-ai-widget-form').dispatchEvent(new Event('submit'));
    };

    function appendUserWidgetMsg(text) {
        const div = document.createElement('div');
        div.style.cssText = 'display: flex; justify-content: flex-end;';
        div.innerHTML = `
            <div style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; padding: 8px 12px; border-radius: 12px 12px 2px 12px; font-size: 12.5px; line-height: 1.4; max-width: 82%; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                ${escapeWidgetHtml(text)}
            </div>
        `;
        messagesBox.appendChild(div);
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function appendBotWidgetLoading(id) {
        const div = document.createElement('div');
        div.id = id;
        div.style.cssText = 'display: flex; gap: 8px; align-items: flex-start;';
        div.innerHTML = `
            <div style="background: #2563eb; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                <i class="fas fa-robot"></i>
            </div>
            <div style="background: #ffffff; padding: 8px 12px; border-radius: 12px 12px 12px 2px; border: 1px solid #e2e8f0; font-size: 12px; color: #64748b;">
                <i class="fas fa-spinner fa-spin mr-1"></i> Consultando DeepSeek...
            </div>
        `;
        messagesBox.appendChild(div);
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function appendBotWidgetMsg(markdownText) {
        const div = document.createElement('div');
        div.style.cssText = 'display: flex; gap: 8px; align-items: flex-start;';
        const msgId = 'w-msg-' + Date.now();
        div.innerHTML = `
            <div style="background: #2563eb; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0;">
                <i class="fas fa-robot"></i>
            </div>
            <div style="background: #ffffff; padding: 9px 12px; border-radius: 12px 12px 12px 2px; border: 1px solid #e2e8f0; font-size: 12.5px; color: #1e293b; line-height: 1.45; max-width: 85%; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div id="${msgId}">${formatWidgetMarkdown(markdownText)}</div>
                <div style="margin-top: 6px; display: flex; justify-content: flex-end; gap: 8px;">
                    <span onclick="speakWidgetText('${msgId}')" title="Escuchar respuesta" style="cursor: pointer; color: #94a3b8; font-size: 11px;">
                        <i class="fas fa-volume-up"></i>
                    </span>
                    <span onclick="copyWidgetText('${msgId}')" title="Copiar" style="cursor: pointer; color: #94a3b8; font-size: 11px;">
                        <i class="far fa-copy"></i>
                    </span>
                </div>
            </div>
        `;
        messagesBox.appendChild(div);
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function formatWidgetMarkdown(text) {
        if (!text) return '';
        let parsed = escapeWidgetHtml(text);
        parsed = parsed.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        parsed = parsed.replace(/\n/g, '<br>');
        return parsed;
    }

    function escapeWidgetHtml(text) {
        const d = document.createElement('div');
        d.innerText = text;
        return d.innerHTML;
    }

    window.toggleWidgetVoice = function() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            alert('Reconocimiento de voz no soportado en este navegador.');
            return;
        }

        if (isWidgetRecording) {
            stopWidgetVoice();
            return;
        }

        widgetRecognition = new SpeechRecognition();
        widgetRecognition.lang = 'es-PE';
        widgetRecognition.continuous = false;

        widgetRecognition.onstart = () => {
            isWidgetRecording = true;
            micBtn.style.background = '#ef4444';
            micBtn.style.color = '#ffffff';
        };

        widgetRecognition.onresult = (e) => {
            let transcript = e.results[0][0].transcript;
            transcript = transcript.replace(/\b(este|eh+|em+|o sea|bueno)\b/gi, '').trim();
            if (transcript) {
                transcript = transcript.charAt(0).toUpperCase() + transcript.slice(1);
            }
            input.value = transcript;
        };

        widgetRecognition.onerror = () => stopWidgetVoice();
        widgetRecognition.onend = () => stopWidgetVoice();
        widgetRecognition.start();
    };

    function stopWidgetVoice() {
        if (widgetRecognition && isWidgetRecording) {
            try { widgetRecognition.stop(); } catch(e) {}
        }
        isWidgetRecording = false;
        micBtn.style.background = '#f1f5f9';
        micBtn.style.color = '#64748b';
    }

    window.speakWidgetText = function(id) {
        if (!('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const el = document.getElementById(id);
        if (!el) return;
        const u = new SpeechSynthesisUtterance(el.innerText.replace(/\|/g, ' ').replace(/[-*•]/g, ''));
        u.lang = 'es-PE';
        u.rate = 1.05;
        window.speechSynthesis.speak(u);
    };

    window.copyWidgetText = function(id) {
        const el = document.getElementById(id);
        if (el) {
            navigator.clipboard.writeText(el.innerText);
        }
    };
})();
</script>
