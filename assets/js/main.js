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
    document.addEventListener("click", function (e) {
        var thumb = e.target.closest(".detail-thumb");
        if (!thumb) return;
        var gallery = thumb.closest("#product-gallery");
        var main = gallery && gallery.querySelector("#gallery-main");
        if (!main) return;
        main.src = thumb.getAttribute("data-src");
        gallery.querySelectorAll(".detail-thumb.active").forEach(function (el) {
            el.classList.remove("active");
        });
        thumb.classList.add("active");
    });
})();
