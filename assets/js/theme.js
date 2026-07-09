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

  function isDeleteLink(link) {
    if (!link || link.tagName !== 'A') return false;
    if (link.dataset.noConfirm === 'true') return false;
    if (link.id === 'globalDeleteConfirmBtn' || link.closest('#globalDeleteModal')) return false;

    var href = link.getAttribute('href') || '';
    if (!href || href === '#') return false;
    if (!/action=delete|delete_prescription|deletebulk/i.test(href)) return false;

    return (
      link.classList.contains('btn-outline-danger') ||
      link.classList.contains('btn-danger') ||
      link.classList.contains('btn-delete-invoice') ||
      link.classList.contains('btn-delete-prescription') ||
      link.title === 'Delete' ||
      !!link.querySelector('.bi-trash, .fa-trash, .fa-trash-o')
    );
  }

  function getDeleteTitle(link) {
    if (link.dataset.confirmTitle) return link.dataset.confirmTitle;

    var href = link.getAttribute('href') || '';
    if (/delete_prescription/i.test(href) || link.dataset.prescription) return 'Delete Prescription?';
    if (link.dataset.invoice) return 'Delete Invoice?';

    var path = window.location.pathname.toLowerCase();
    if (path.indexOf('/invoices') !== -1) return 'Delete Invoice?';
    if (path.indexOf('/patients') !== -1) return 'Delete Patient?';
    if (path.indexOf('/services') !== -1) return 'Delete Service?';
    if (path.indexOf('/user') !== -1) return 'Delete User?';
    if (path.indexOf('/currency') !== -1) return 'Delete Currency?';
    if (path.indexOf('/autonumber') !== -1) return 'Delete Record?';
    if (path.indexOf('/settings') !== -1) return 'Delete Item?';

    return 'Confirm Delete?';
  }

  function getDeleteConfirmText(link) {
    var title = getDeleteTitle(link);
    if (title.indexOf('Delete ') === 0) {
      return title.replace('?', '');
    }
    return 'Delete';
  }

  function buildDeleteDetails(link) {
    var items = [];

    if (link.dataset.invoice) {
      items.push(['Invoice No.', link.dataset.invoice]);
      if (link.dataset.patient) items.push(['Patient', link.dataset.patient]);
      if (link.dataset.amount) {
        items.push(['Total Amount', ((link.dataset.currency || '') + ' ' + link.dataset.amount).trim()]);
      }
      return items;
    }

    if (link.dataset.prescription) {
      items.push(['Prescription No.', link.dataset.prescription]);
      if (link.dataset.patient) items.push(['Patient', link.dataset.patient]);
      if (link.dataset.medicine) items.push(['Medicine', link.dataset.medicine]);
      return items;
    }

    var row = link.closest('tr');
    var table = link.closest('table');
    if (!row || !table) return items;

    var headers = table.querySelectorAll('thead th');
    for (var i = 0; i < headers.length && items.length < 3; i++) {
      var label = headers[i].textContent.replace(/\s+/g, ' ').trim();
      if (!label || /action/i.test(label)) continue;

      var cell = row.cells[i];
      if (!cell || cell.contains(link)) continue;

      var value = cell.textContent.replace(/\s+/g, ' ').trim();
      if (value) items.push([label, value]);
    }

    return items;
  }

  function renderDeleteDetails(items) {
    var container = document.getElementById('globalDeleteDetails');
    if (!container) return;

    container.innerHTML = '';
    if (!items.length) {
      container.classList.add('d-none');
      return;
    }

    items.forEach(function (item) {
      var dt = document.createElement('dt');
      dt.textContent = item[0];
      var dd = document.createElement('dd');
      dd.textContent = item[1];
      container.appendChild(dt);
      container.appendChild(dd);
    });

    container.classList.remove('d-none');
  }

  function initDeleteConfirm() {
    if (typeof bootstrap === 'undefined') return;

    var modalEl = document.getElementById('globalDeleteModal');
    if (!modalEl) return;

    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    var confirmBtn = document.getElementById('globalDeleteConfirmBtn');
    var titleEl = document.getElementById('globalDeleteModalLabel');
    var messageEl = document.getElementById('globalDeleteMessage');
    var confirmTextEl = document.getElementById('globalDeleteConfirmText');
    var pendingDeleteUrl = '';

    confirmBtn.addEventListener('click', function () {
      if (!pendingDeleteUrl) return;
      window.location.href = pendingDeleteUrl;
    });

    document.addEventListener('click', function (e) {
      var link = e.target.closest('a');
      if (!isDeleteLink(link)) return;

      e.preventDefault();
      e.stopPropagation();

      pendingDeleteUrl = link.getAttribute('href');
      titleEl.textContent = getDeleteTitle(link);
      messageEl.textContent = link.dataset.confirmMessage || 'Are you sure you want to delete this item? This action cannot be undone.';
      confirmTextEl.textContent = getDeleteConfirmText(link);
      renderDeleteDetails(buildDeleteDetails(link));

      modal.show();
    }, true);
  }

  function initUserDropdown() {
    document.querySelectorAll('.user-avatar--photo').forEach(function (img) {
      img.addEventListener('error', function () {
        img.classList.add('d-none');
        var wrap = img.closest('.user-avatar-wrap');
        if (!wrap) return;
        var initials = wrap.querySelector('.user-avatar--initials');
        if (initials) initials.classList.remove('d-none');
      }, { once: true });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initTheme();
    initSidebar();
    initModalShim();
    initDeleteConfirm();
    initUserDropdown();
  });
})();
