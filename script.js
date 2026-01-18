// ========== Sidebar Toggle untuk Mobile ==========
document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.getElementById("menuToggle");
  const sidebar = document.getElementById("sidebar");

  if (menuToggle && sidebar) {
    menuToggle.addEventListener("click", function (e) {
      e.stopPropagation();
      sidebar.classList.toggle("active");

      // Animate hamburger
      this.classList.toggle("active");
    });

    // Close sidebar ketika klik di luar sidebar (mobile)
    document.addEventListener("click", function (event) {
      if (window.innerWidth <= 768) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnToggle = menuToggle.contains(event.target);

        if (
          !isClickInsideSidebar &&
          !isClickOnToggle &&
          sidebar.classList.contains("active")
        ) {
          sidebar.classList.remove("active");
          menuToggle.classList.remove("active");
        }
      }
    });

    // Close sidebar on window resize
    window.addEventListener("resize", function () {
      if (window.innerWidth > 768) {
        sidebar.classList.remove("active");
        menuToggle.classList.remove("active");
      }
    });
  }

  // ========== Chart Initialization (Placeholder) ==========
  const chartCanvas = document.getElementById("cashFlowChart");
  if (chartCanvas) {
    // Placeholder untuk Chart.js
    // Akan diimplementasikan dengan Chart.js di tahap berikutnya
    const ctx = chartCanvas.getContext("2d");

    // Dummy chart visualization
    ctx.fillStyle = "#334155";
    ctx.fillRect(0, 0, chartCanvas.width, chartCanvas.height);

    ctx.fillStyle = "#94a3b8";
    ctx.font = "14px Segoe UI";
    ctx.textAlign = "center";
    ctx.fillText(
      "Grafik akan ditampilkan dengan Chart.js",
      chartCanvas.width / 2,
      chartCanvas.height / 2,
    );
    ctx.fillText(
      "(Tahap Pengembangan Selanjutnya)",
      chartCanvas.width / 2,
      chartCanvas.height / 2 + 20,
    );
  }

  // ========== Progress Bar Animation ==========
  const progressBars = document.querySelectorAll(".progress-fill");

  // Animate progress bars on load
  setTimeout(() => {
    progressBars.forEach((bar) => {
      const width = bar.style.width;
      bar.style.width = "0%";
      setTimeout(() => {
        bar.style.width = width;
      }, 100);
    });
  }, 300);

  // ========== Stat Cards Animation ==========
  const statCards = document.querySelectorAll(".stat-card");

  statCards.forEach((card, index) => {
    card.style.opacity = "0";
    card.style.transform = "translateY(20px)";

    setTimeout(() => {
      card.style.transition = "all 0.5s ease";
      card.style.opacity = "1";
      card.style.transform = "translateY(0)";
    }, index * 100);
  });

  // ========== Transaction Items Hover Effect ==========
  const transactionItems = document.querySelectorAll(".transaction-item");

  transactionItems.forEach((item) => {
    item.addEventListener("mouseenter", function () {
      this.style.transform = "translateX(4px)";
    });

    item.addEventListener("mouseleave", function () {
      this.style.transform = "translateX(0)";
    });
  });

  // ========== Format Number Input (untuk form di masa depan) ==========
  function formatRupiah(angka, prefix = "Rp ") {
    const number_string = angka.replace(/[^,\d]/g, "").toString();
    const split = number_string.split(",");
    const sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
      const separator = sisa ? "." : "";
      rupiah += separator + ribuan.join(".");
    }

    rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
    return prefix + rupiah;
  }

  // ========== Auto Hide Alert Messages ==========
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.transition = "opacity 0.5s ease";
      alert.style.opacity = "0";
      setTimeout(() => {
        alert.remove();
      }, 500);
    }, 5000); // Hide after 5 seconds
  });

  // ========== Smooth Scroll untuk navigasi ==========
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    });
  });

  // ========== Dynamic Greeting Based on Time ==========
  function updateGreeting() {
    const header = document.querySelector(".header-left h1");
    if (header && header.textContent.includes("Selamat")) {
      const hour = new Date().getHours();
      const name = header.textContent.split(",")[1];
      let greeting;

      if (hour < 12) {
        greeting = "Selamat Pagi";
      } else if (hour < 15) {
        greeting = "Selamat Siang";
      } else if (hour < 18) {
        greeting = "Selamat Sore";
      } else {
        greeting = "Selamat Malam";
      }

      header.textContent = greeting + "," + name;
    }
  }

  updateGreeting();

  // ========== Console Info ==========
  console.log(
    "%c💰 Aplikasi Keuangan Harian",
    "color: #6366f1; font-size: 20px; font-weight: bold;",
  );
  console.log(
    "%cVersi: 1.0.0 (Development)",
    "color: #94a3b8; font-size: 12px;",
  );
  console.log(
    "%cDibuat dengan ❤️ menggunakan PHP, HTML, CSS, dan JavaScript",
    "color: #94a3b8; font-size: 12px;",
  );
});

// ========== Utility Functions ==========

// Format angka ke Rupiah
function toRupiah(angka) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(angka);
}

// Format tanggal ke Indonesia
function toIndonesianDate(date) {
  const options = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  return new Date(date).toLocaleDateString("id-ID", options);
}

// Debounce function untuk search/filter
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Show notification (untuk fitur masa depan)
function showNotification(message, type = "info") {
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.textContent = message;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.classList.add("show");
  }, 100);

  setTimeout(() => {
    notification.classList.remove("show");
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 3000);
}
