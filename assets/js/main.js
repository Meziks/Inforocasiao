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

    // Pré-visualização da imagem escolhida no formulário de artigo
    document.addEventListener("change", function (e) {
        if (e.target && e.target.matches('input[type="file"][name="image"]')) {
            var input = e.target;
            if (!input.files || !input.files[0]) return;
            var wrap = input.closest("label");
            if (!wrap) return;
            var existing = wrap.querySelector(".file-preview");
            var url = URL.createObjectURL(input.files[0]);
            if (existing) {
                existing.src = url;
            } else {
                var img = document.createElement("img");
                img.className = "file-preview";
                img.src = url;
                img.style.cssText = "width:80px;height:80px;object-fit:cover;border-radius:8px;margin-top:8px;border:1px solid #e6e8ef;";
                wrap.appendChild(img);
            }
        }
    });
})();
