// GastroSmart Surakarta — Main JS
// Re-init Lucide icons after dynamic content
document.addEventListener("DOMContentLoaded", () => {
  if (window.lucide) lucide.createIcons();

  // Animate stat values (count-up effect)
  document.querySelectorAll("[data-countup]").forEach((el) => {
    const target = parseFloat(el.dataset.countup);
    const decimals = el.dataset.decimals ? parseInt(el.dataset.decimals) : 0;
    const duration = 800;
    const start = performance.now();
    const from = 0;
    const update = (now) => {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const ease = 1 - Math.pow(1 - progress, 3);
      const current = from + (target - from) * ease;
      el.textContent = current.toFixed(decimals);
      if (progress < 1) requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
  });

  // Animate score bars
  document.querySelectorAll(".score-bar-fill[data-width]").forEach((el) => {
    setTimeout(() => {
      el.style.width = el.dataset.width + "%";
    }, 100);
  });

  const sidebar = document.getElementById("sidebar");
  const sidebarOverlay = document.getElementById("sidebarOverlay");
  const menuButton = document.querySelector(".menu-btn");

  const syncSidebarState = (isOpen) => {
    if (!sidebar || !sidebarOverlay || !menuButton) {
      return;
    }

    sidebar.classList.toggle("open", isOpen);
    sidebarOverlay.classList.toggle("visible", isOpen);
    menuButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
    document.body.style.overflow = isOpen ? "hidden" : "";
  };

  window.toggleSidebar = () => {
    if (!sidebar) {
      return;
    }

    syncSidebarState(!sidebar.classList.contains("open"));
  };

  window.closeSidebar = () => syncSidebarState(false);

  if (menuButton) {
    menuButton.setAttribute("aria-label", "Buka menu sidebar");
    menuButton.setAttribute("aria-controls", "sidebar");
    menuButton.setAttribute("aria-expanded", "false");
    menuButton.type = "button";
  }

  if (sidebarOverlay) {
    sidebarOverlay.addEventListener("click", () => syncSidebarState(false));
  }

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      syncSidebarState(false);
    }
  });

  window.addEventListener("resize", () => {
    if (window.innerWidth > 900) {
      syncSidebarState(false);
    }
  });

  document.querySelectorAll(".sidebar-nav a").forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth <= 900) {
        syncSidebarState(false);
      }
    });
  });
});

// Modal helpers
function openModal(id) {
  const m = document.getElementById(id);
  if (m) {
    m.classList.add("open");
    document.body.style.overflow = "hidden";
  }
}
function closeModal(id) {
  const m = document.getElementById(id);
  if (m) {
    m.classList.remove("open");
    document.body.style.overflow = "";
  }
}
// Close modal on backdrop click
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("modal-backdrop")) {
    e.target.classList.remove("open");
    document.body.style.overflow = "";
  }
});
