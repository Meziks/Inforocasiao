/* Inforocasião — interações leves do lado do cliente */
(function () {
    "use strict";

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
