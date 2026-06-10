/**
 * DentalClinic Theme — Sidebar, Dark Mode
 */
(function () {
  'use strict';

  var STORAGE_KEY = 'dc-theme';
  var SIDEBAR_KEY = 'dc-sidebar-collapsed';

  function getPreferredTheme() {
    var stored = localStorage.getItem(STORAGE_KEY);
    if (stored) return stored;
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function setTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    localStorage.setItem(STORAGE_KEY, theme);
    updateThemeIcon(theme);
  }

  function updateThemeIcon(theme) {
    var btn = document.getElementById('themeToggle');
    if (!btn) return;
    var icon = btn.querySelector('i');
    if (!icon) return;
    icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    btn.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
  }

  function initTheme() {
    setTheme(getPreferredTheme());
    var toggle = document.getElementById('themeToggle');
    if (toggle) {
      toggle.addEventListener('click', function () {
        var current = document.documentElement.getAttribute('data-bs-theme') || 'light';
        setTheme(current === 'dark' ? 'light' : 'dark');
      });
    }
  }

  function initSidebar() {
    var sidebar = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebarBackdrop');
    var toggleBtn = document.getElementById('sidebarToggle');
    var collapseBtn = document.getElementById('sidebarCollapse');

    if (localStorage.getItem(SIDEBAR_KEY) === 'true' && window.innerWidth >= 992) {
      document.body.classList.add('sidebar-collapsed');
    }

    function openSidebar() {
      if (sidebar) sidebar.classList.add('show');
      if (backdrop) backdrop.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
      if (sidebar) sidebar.classList.remove('show');
      if (backdrop) backdrop.classList.remove('show');
      document.body.style.overflow = '';
    }

    if (toggleBtn) {
      toggleBtn.addEventListener('click', function () {
        if (sidebar && sidebar.classList.contains('show')) {
          closeSidebar();
        } else {
          openSidebar();
        }
      });
    }

    if (backdrop) {
      backdrop.addEventListener('click', closeSidebar);
    }

    if (collapseBtn) {
      collapseBtn.addEventListener('click', function () {
        document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem(SIDEBAR_KEY, document.body.classList.contains('sidebar-collapsed'));
      });
    }

    window.addEventListener('resize', function () {
      if (window.innerWidth >= 992) {
        closeSidebar();
      }
    });
  }

  // Bootstrap 3 → 5 modal shim for legacy pages
  function initModalShim() {
    if (typeof jQuery === 'undefined' || typeof bootstrap === 'undefined') return;

    if (!jQuery.fn.modal) {
      jQuery.fn.modal = function (action) {
        return this.each(function () {
          var instance = bootstrap.Modal.getOrCreateInstance(this);
          if (action === 'show') instance.show();
          else if (action === 'hide') instance.hide();
          else if (action === 'toggle') instance.toggle();
          else if (!action) instance.show();
        });
      };
    }

    jQuery(document).on('click', '[data-dismiss="modal"]', function () {
      var modal = jQuery(this).closest('.modal');
      if (modal.length) {
        var instance = bootstrap.Modal.getInstance(modal[0]);
        if (instance) instance.hide();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initTheme();
    initSidebar();
    initModalShim();
  });
})();
