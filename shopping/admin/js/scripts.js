/*!
    * Start Bootstrap - SB Admin v7.0.4 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2021 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }
    // Active sidebar highlighting
    let path = window.location.pathname.split('/').pop();
    if (path) {
        // Map common paths to their main category so the accordion opens properly
        let targetPath = path;
        if (path.includes('order')) { targetPath = 'all-orders.php'; }
        else if (path.includes('category.php') || path.includes('categories.php')) { targetPath = 'manage-categories.php'; }
        else if (path.includes('subcategory.php') || path.includes('subcategories.php')) { targetPath = 'manage-subcategories.php'; }
        else if (path.includes('product.php') || path.includes('products.php')) { targetPath = 'manage-products.php'; }
        else if (path.includes('user')) { targetPath = 'registered-users.php'; }
        
        let target = document.querySelector('.nav-link[href="' + targetPath + '"]');
        if (!target) {
            target = document.querySelector('.nav-link[href="' + path + '"]');
        }

        if (target) {
            target.classList.add('active');
            const collapseParent = target.closest('.collapse');
            if (collapseParent) {
                collapseParent.classList.add('show');
                const trigger = document.querySelector('[data-bs-target="#' + collapseParent.id + '"]');
                if (trigger) {
                    trigger.classList.remove('collapsed');
                    trigger.classList.add('active');
                    trigger.setAttribute('aria-expanded', 'true');
                }
            }
        }
    }

});
