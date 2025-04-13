/**
 * MitaiCode - JavaScript para el Dashboard de Estudiantes
 */

document.addEventListener('DOMContentLoaded', function() {
    // Gestionar pestañas del dashboard
    const tabLinks = document.querySelectorAll('#studentDashboardTabs .nav-link');
    const tabContents = document.querySelectorAll('.tab-pane');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Desactivar todas las pestañas
            tabLinks.forEach(tab => tab.classList.remove('active'));
            tabContents.forEach(content => {
                content.classList.remove('show', 'active');
            });
            
            // Activar la pestaña seleccionada
            this.classList.add('active');
            const target = this.getAttribute('data-bs-target');
            const tabContent = document.querySelector(target);
            if (tabContent) {
                tabContent.classList.add('show', 'active');
            }
        });
    });
    
    // Filtrado de misiones
    const searchMissionsInput = document.getElementById('searchMissions');
    const searchMissionsBtn = document.getElementById('searchMissionsBtn');
    const missionCategoryFilter = document.getElementById('missionCategoryFilter');
    const missionSortOrder = document.getElementById('missionSortOrder');
    
    if (searchMissionsBtn) {
        searchMissionsBtn.addEventListener('click', filterMissions);
    }
    
    if (searchMissionsInput) {
        searchMissionsInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                filterMissions();
            }
        });
    }
    
    if (missionCategoryFilter) {
        missionCategoryFilter.addEventListener('change', filterMissions);
    }
    
    if (missionSortOrder) {
        missionSortOrder.addEventListener('change', filterMissions);
    }
    
    function filterMissions() {
        const searchTerm = searchMissionsInput ? searchMissionsInput.value.toLowerCase() : '';
        const category = missionCategoryFilter ? missionCategoryFilter.value : 'all';
        const sortOrder = missionSortOrder ? missionSortOrder.value : 'newest';
        
        // Aquí implementarías la lógica para filtrar misiones
        // Por ahora, solo mostraremos un mensaje en la consola
        console.log(`Filtrando misiones. Búsqueda: "${searchTerm}", Categoría: ${category}, Orden: ${sortOrder}`);
        
        // En una implementación real, harías una petición AJAX al servidor
        // o filtrarías en el cliente si ya tienes los datos
    }
    
    // Selector de nivel en desafíos
    const levelButtons = document.querySelectorAll('.btn-group [data-level]');
    
    levelButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Quitar clase active de todos los botones
            levelButtons.forEach(btn => btn.classList.remove('active', 'btn-primary'));
            levelButtons.forEach(btn => btn.classList.add('btn-outline-primary'));
            
            // Activar el botón seleccionado
            this.classList.remove('btn-outline-primary');
            this.classList.add('active', 'btn-primary');
            
            const level = this.getAttribute('data-level');
            console.log(`Nivel seleccionado: ${level}`);
            
            // Aquí cargarías los desafíos del nivel seleccionado
        });
    });
}); 