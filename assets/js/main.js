/* Inforocasião — interações leves do lado do cliente */
(function () {
    "use strict";

    // Menu mobile: abrir/fechar ao clicar no botão "☰"
    document.addEventListener("click", function (e) {
        var toggle = e.target.closest(".nav-toggle");
        var nav = document.getElementById("main-nav");
        if (!nav) return;

        if (toggle) {
            var open = nav.classList.toggle("open");
            toggle.setAttribute("aria-expanded", open ? "true" : "false");
            return;
        }
        // Fechar ao clicar num link do menu ou fora do menu
        if (nav.classList.contains("open") && !e.target.closest("#main-nav")) {
            nav.classList.remove("open");
            var btn = document.querySelector(".nav-toggle");
            if (btn) btn.setAttribute("aria-expanded", "false");
        }
    });

    // Pré-visualização da imagem escolhida em cada slot do formulário de artigo
    document.addEventListener("change", function (e) {
        if (e.target && e.target.matches('.image-slot input[type="file"]')) {
            var input = e.target;
            if (!input.files || !input.files[0]) return;
            var wrap = input.closest(".image-slot");
            if (!wrap) return;
            var existing = wrap.querySelector(".file-preview");
            var url = URL.createObjectURL(input.files[0]);
            if (existing) {
                existing.src = url;
            } else {
                var img = document.createElement("img");
                img.className = "file-preview";
                img.src = url;
                img.style.cssText = "width:56px;height:56px;object-fit:cover;border-radius:8px;margin-bottom:6px;border:1px solid #e6e8ef;";
                wrap.insertBefore(img, input);
            }
        }
    });

    // Ficha de produto: trocar a imagem principal ao clicar numa miniatura
    var currentGalleryIndex = 0;
    document.addEventListener("click", function (e) {
        var thumb = e.target.closest(".detail-thumb");
        if (!thumb) return;
        var gallery = thumb.closest("#product-gallery");
        var main = gallery && gallery.querySelector("#gallery-main");
        if (!main) return;
        main.src = thumb.getAttribute("data-src");
        currentGalleryIndex = parseInt(thumb.getAttribute("data-index"), 10) || 0;
        gallery.querySelectorAll(".detail-thumb.active").forEach(function (el) {
            el.classList.remove("active");
        });
        thumb.classList.add("active");
    });

    // Ficha de produto: lightbox (ver foto em tamanho grande)
    (function () {
        var lightbox = document.getElementById("lightbox");
        if (!lightbox) return;
        var lbImg   = document.getElementById("lightbox-img");
        var lbCount = document.getElementById("lightbox-count");
        var images  = Array.prototype.map.call(
            document.querySelectorAll("#product-gallery .detail-thumb"),
            function (t) { return t.getAttribute("data-src"); }
        );
        if (images.length === 0) {
            var mainImg = document.getElementById("gallery-main");
            if (mainImg) images = [mainImg.getAttribute("src")];
        }

        function show(i) {
            if (images.length === 0) return;
            currentGalleryIndex = (i + images.length) % images.length;
            lbImg.setAttribute("src", images[currentGalleryIndex]);
            if (lbCount) lbCount.textContent = (currentGalleryIndex + 1) + " / " + images.length;
        }
        function open(i) {
            show(i);
            lightbox.classList.add("open");
            lightbox.setAttribute("aria-hidden", "false");
            document.body.classList.add("no-scroll");
        }
        function close() {
            lightbox.classList.remove("open");
            lightbox.setAttribute("aria-hidden", "true");
            document.body.classList.remove("no-scroll");
        }

        document.addEventListener("click", function (e) {
            if (e.target.closest(".gallery-zoom") || e.target.closest(".detail-media-main img")) {
                open(currentGalleryIndex);
                return;
            }
            if (e.target.closest(".lightbox-close")) { close(); return; }
            if (e.target.closest(".lightbox-prev")) { show(currentGalleryIndex - 1); return; }
            if (e.target.closest(".lightbox-next")) { show(currentGalleryIndex + 1); return; }
            if (e.target === lightbox) { close(); return; } // clicar fora da imagem fecha
        });

        document.addEventListener("keydown", function (e) {
            if (!lightbox.classList.contains("open")) return;
            if (e.key === "Escape") close();
            if (e.key === "ArrowLeft") show(currentGalleryIndex - 1);
            if (e.key === "ArrowRight") show(currentGalleryIndex + 1);
        });
    })();

    // Página inicial: carrossel de produtos em destaque (roda automaticamente)
    (function () {
        var carousel = document.getElementById("featured-carousel");
        if (!carousel) return;
        var track  = carousel.querySelector(".carousel-track");
        var slides = Array.prototype.slice.call(track.children);
        var dotsWrap = carousel.querySelector(".carousel-dots");
        var index = 0;
        var timer = null;

        slides.forEach(function (_, i) {
            var dot = document.createElement("button");
            dot.type = "button";
            dot.setAttribute("aria-label", "Ir para o artigo " + (i + 1));
            dot.addEventListener("click", function () { goTo(i); restart(); });
            dotsWrap.appendChild(dot);
        });
        var dots = Array.prototype.slice.call(dotsWrap.children);

        function slideStep() {
            var style = getComputedStyle(track);
            var gap = parseFloat(style.gap || style.columnGap || "0");
            return slides[0].getBoundingClientRect().width + gap;
        }
        function goTo(i) {
            index = ((i % slides.length) + slides.length) % slides.length;
            track.style.transform = "translateX(-" + (index * slideStep()) + "px)";
            dots.forEach(function (d, di) { d.classList.toggle("active", di === index); });
        }
        function restart() {
            if (timer) clearInterval(timer);
            timer = setInterval(function () { goTo(index + 1); }, 4500);
        }

        carousel.querySelector(".carousel-prev").addEventListener("click", function () { goTo(index - 1); restart(); });
        carousel.querySelector(".carousel-next").addEventListener("click", function () { goTo(index + 1); restart(); });
        carousel.addEventListener("mouseenter", function () { if (timer) clearInterval(timer); });
        carousel.addEventListener("mouseleave", restart);
        window.addEventListener("resize", function () { goTo(index); });

        goTo(0);
        restart();
    })();
})();
