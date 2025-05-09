<?php
// Подключаем виджет стресса на все страницы
?>
<div id="pulse-widget-container"></div>
<script src="/widgets/pulse-widget/pulse-widget.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализируем виджет только если он ещё не существует
        if (!window.pulseWidgetInstance) {
            window.pulseWidgetInstance = new PulseWidget('pulse-widget-container');
        }
        
        // Восстанавливаем состояние виджета при каждой загрузке страницы
        const savedState = localStorage.getItem('pulseWidgetState');
        if (savedState) {
            const state = JSON.parse(savedState);
            const container = document.getElementById('pulse-widget-container');
            const widget = container.querySelector('.pulse-widget');
            
            // Восстанавливаем видимость
            container.style.display = state.isVisible ? 'block' : 'none';
            
            // Восстанавливаем позицию
            if (state.position) {
                widget.style.left = state.position.left;
                widget.style.top = state.position.top;
            }
            
            // Восстанавливаем состояние записи
            if (state.isRecording) {
                window.pulseWidgetInstance.startRecording();
            }
        }
        
        // Функция для переключения видимости виджета
        window.togglePulseWidget = function() {
            const container = document.getElementById('pulse-widget-container');
            if (container) {
                const isVisible = container.style.display !== 'none';
                container.style.display = isVisible ? 'none' : 'block';
                
                // Сохраняем состояние видимости
                const state = JSON.parse(localStorage.getItem('pulseWidgetState') || '{}');
                state.isVisible = !isVisible;
                localStorage.setItem('pulseWidgetState', JSON.stringify(state));
            }
        };
    });
</script> 