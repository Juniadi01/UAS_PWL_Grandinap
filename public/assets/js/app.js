/* =====================================================================
   GrandInap - app.js
   Tema gelap/terang, toast, favorit (AJAX), reveal animasi, dll.
   Bergantung pada window.GI = { base, auth } yang di-set di layout.
   ===================================================================== */
(function () {
  "use strict";
  const GI = window.GI || { base: "", auth: false };

  /* ---------- 1. Tema gelap/terang (tersimpan di localStorage) ---------- */
  const THEME_KEY = "gi-theme";
  const root = document.documentElement;

  function applyTheme(t) {
    root.setAttribute("data-bs-theme", t);
    document.querySelectorAll("[data-theme-toggle] i").forEach(function (ic) {
      ic.className = t === "dark" ? "bi bi-sun-fill" : "bi bi-moon-stars-fill";
    });
  }
  let saved = "light";
  try { saved = localStorage.getItem(THEME_KEY) || "light"; } catch (e) {}
  applyTheme(saved);

  document.addEventListener("click", function (e) {
    const btn = e.target.closest("[data-theme-toggle]");
    if (!btn) return;
    const next = root.getAttribute("data-bs-theme") === "dark" ? "light" : "dark";
    applyTheme(next);
    try { localStorage.setItem(THEME_KEY, next); } catch (e) {}
  });

  /* ---------- 2. Navbar menyusut saat scroll ---------- */
  const nav = document.querySelector(".gi-nav");
  if (nav) {
    const onScroll = () => nav.classList.toggle("is-scrolled", window.scrollY > 12);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  /* ---------- 3. Toast (notifikasi) ---------- */
  function toast(type, message) {
    if (typeof Swal === "undefined") return alert(message);
    const map = { success: "success", danger: "error", warning: "warning", info: "info" };
    Swal.fire({
      toast: true, position: "top-end", timer: 3200, timerProgressBar: true,
      showConfirmButton: false, icon: map[type] || "info", title: message,
      didOpen: (el) => {
        el.addEventListener("mouseenter", Swal.stopTimer);
        el.addEventListener("mouseleave", Swal.resumeTimer);
      },
    });
  }
  window.giToast = toast;
  // Flash dari server (PHP) -> tampilkan sebagai toast
  if (window.GI_FLASH && window.GI_FLASH.message) {
    toast(window.GI_FLASH.type, window.GI_FLASH.message);
  }

  /* ---------- 4. Favorit / Wishlist (AJAX) ---------- */
  function markWishlisted(ids) {
    document.querySelectorAll("[data-wishlist]").forEach(function (btn) {
      const id = parseInt(btn.dataset.wishlist, 10);
      const active = ids.indexOf(id) !== -1;
      btn.classList.toggle("is-active", active);
      const ic = btn.querySelector("i");
      if (ic) ic.className = active ? "bi bi-heart-fill" : "bi bi-heart";
      btn.setAttribute("aria-pressed", active ? "true" : "false");
    });
  }
  if (GI.auth && document.querySelector("[data-wishlist]")) {
    fetch(GI.base + "wishlist/ids")
      .then((r) => r.json())
      .then((d) => markWishlisted(d.ids || []))
      .catch(() => {});
  }
  document.addEventListener("click", function (e) {
    const btn = e.target.closest("[data-wishlist]");
    if (!btn) return;
    e.preventDefault();
    if (!GI.auth) {
      Swal.fire({
        icon: "info", title: "Masuk dulu, yuk",
        text: "Login untuk menyimpan kamar favoritmu.",
        confirmButtonText: "Login", showCancelButton: true, cancelButtonText: "Nanti",
        confirmButtonColor: "#0A1F3A",
      }).then((r) => { if (r.isConfirmed) window.location = GI.base + "auth/login"; });
      return;
    }
    const id = btn.dataset.wishlist;
    const fd = new FormData(); fd.append("room_id", id);
    fetch(GI.base + "wishlist/toggle", { method: "POST", body: fd })
      .then((r) => r.json())
      .then((d) => {
        if (!d.ok) return toast("warning", d.message || "Gagal.");
        btn.classList.toggle("is-active", d.added);
        btn.classList.remove("pop"); void btn.offsetWidth; btn.classList.add("pop");
        const ic = btn.querySelector("i");
        if (ic) ic.className = d.added ? "bi bi-heart-fill" : "bi bi-heart";
        btn.setAttribute("aria-pressed", d.added ? "true" : "false");
        toast(d.added ? "success" : "info", d.message);
        // Jika sedang di halaman favorit & dihapus -> hilangkan kartunya
        if (!d.added && document.body.dataset.page === "wishlist") {
          const col = btn.closest(".col-wish"); if (col) col.remove();
        }
      })
      .catch(() => toast("danger", "Terjadi kesalahan koneksi."));
  });

  /* ---------- 5. Konfirmasi hapus & aksi (SweetAlert2) ---------- */
  document.addEventListener("click", function (e) {
    const del = e.target.closest(".btn-delete");
    if (del) {
      e.preventDefault();
      Swal.fire({
        title: "Hapus data ini?", text: "Tindakan ini tidak bisa dibatalkan.",
        icon: "warning", showCancelButton: true, confirmButtonColor: "#d33",
        confirmButtonText: "Ya, hapus", cancelButtonText: "Batal",
      }).then((r) => { if (r.isConfirmed) window.location = del.getAttribute("href"); });
      return;
    }
    const conf = e.target.closest(".btn-confirm");
    if (conf) {
      e.preventDefault();
      Swal.fire({
        title: conf.dataset.msg || "Lanjutkan tindakan ini?",
        icon: "question", showCancelButton: true, confirmButtonColor: "#0A1F3A",
        confirmButtonText: "Ya", cancelButtonText: "Batal",
      }).then((r) => { if (r.isConfirmed) window.location = conf.getAttribute("href"); });
    }
  });

  /* ---------- 6. Toggle lihat/sembunyikan password ---------- */
  document.addEventListener("click", function (e) {
    const t = e.target.closest("[data-toggle-password]");
    if (!t) return;
    const input = document.querySelector(t.dataset.togglePassword);
    if (!input) return;
    const show = input.type === "password";
    input.type = show ? "text" : "password";
    const ic = t.querySelector("i");
    if (ic) ic.className = show ? "bi bi-eye-slash" : "bi bi-eye";
  });

  /* ---------- 7. Reveal saat scroll ---------- */
  const reveals = document.querySelectorAll(".reveal");
  if ("IntersectionObserver" in window && reveals.length) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => { if (en.isIntersecting) { en.target.classList.add("in"); io.unobserve(en.target); } });
    }, { threshold: .12 });
    reveals.forEach((el) => io.observe(el));
  } else {
    reveals.forEach((el) => el.classList.add("in"));
  }

  /* ---------- 8. Loading state tombol booking ---------- */
  const bookingForm = document.getElementById("bookingForm");
  if (bookingForm) {
    bookingForm.addEventListener("submit", function () {
      const b = document.getElementById("btnBook");
      if (b && !b.disabled) { b.disabled = true; b.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...'; }
    });
  }

  /* ---------- 9. Recently viewed (riwayat lihat kamar, lokal) ---------- */
  window.giRememberRoom = function (room) {
    try {
      let list = JSON.parse(localStorage.getItem("gi-recent") || "[]");
      list = list.filter((x) => x.id !== room.id);
      list.unshift(room);
      localStorage.setItem("gi-recent", JSON.stringify(list.slice(0, 4)));
    } catch (e) {}
  };
  const recentWrap = document.getElementById("recentViewed");
  if (recentWrap) {
    try {
      const list = JSON.parse(localStorage.getItem("gi-recent") || "[]");
      if (list.length) {
        recentWrap.querySelector("[data-recent-grid]").innerHTML = list.map(function (r) {
          return (
            '<div class="col-6 col-md-3"><a class="card room-card h-100 text-decoration-none" href="' + GI.base + "rooms/detail/" + r.id + '">' +
            '<div class="media"><img src="' + r.photo + '" alt="' + r.name + '"></div>' +
            '<div class="card-body py-2"><div class="fw-semibold small text-truncate" style="color:var(--gi-text)">' + r.name + '</div>' +
            '<div class="text-brass fw-bold small">' + r.price + "</div></div></a></div>"
          );
        }).join("");
        recentWrap.classList.remove("d-none");
      }
    } catch (e) {}
  }

  /* 10) Tombol salin teks (mis. hash sandi di panel admin) */
  document.addEventListener("click", function (e) {
    const btn = e.target.closest("[data-copy]");
    if (!btn) return;
    const text = btn.getAttribute("data-copy");
    const done = function () {
      const ic = btn.querySelector("i");
      const prev = ic ? ic.className : "";
      if (ic) ic.className = "bi bi-check-lg";
      if (window.giToast) giToast("Disalin ke papan klip", "success");
      setTimeout(function () { if (ic) ic.className = prev; }, 1200);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(done).catch(done);
    } else {
      const ta = document.createElement("textarea");
      ta.value = text; document.body.appendChild(ta); ta.select();
      try { document.execCommand("copy"); } catch (err) {}
      document.body.removeChild(ta); done();
    }
  });
})();
