/* Inforocasião — interações leves do lado do cliente */
(function () {
    "use strict";

    // Menu mobile: abrir/fechar ao clicar no botão "☰" (site público e gestão)
    document.addEventListener("click", function (e) {
        var toggle = e.target.closest(".nav-toggle");
        if (toggle) {
            var targetId = toggle.getAttribute("aria-controls");
            var nav = targetId && document.getElementById(targetId);
            if (!nav) return;
            var open = nav.classList.toggle("open");
            toggle.setAttribute("aria-expanded", open ? "true" : "false");
            return;
        }
        // Fechar qualquer menu aberto ao clicar num link ou fora dele
        document.querySelectorAll(".main-nav.open").forEach(function (nav) {
            if (!e.target.closest("#" + nav.id) && !e.target.closest(".nav-toggle")) {
                nav.classList.remove("open");
                var btn = document.querySelector('.nav-toggle[aria-controls="' + nav.id + '"]');
                if (btn) btn.setAttribute("aria-expanded", "false");
            }
        });
    });

    // Gestor de imagens do formulário de artigo: upload múltiplo, URLs e
    // reordenação (a primeira imagem da lista é sempre a imagem principal).
    (function () {
        var manager = document.getElementById("image-manager");
        if (!manager) return;

        var list       = document.getElementById("image-manager-list");
        var orderInput = document.getElementById("image-order-input");
        var fileInput  = document.getElementById("image-file-input");
        var urlInput   = document.getElementById("image-url-input");
        var urlAddBtn  = document.getElementById("image-url-add-btn");
        var warning    = document.getElementById("image-manager-warning");
        var maxImages  = parseInt(manager.getAttribute("data-max"), 10) || 4;

        // Estado inicial: imagens já existentes, lidas do HTML gerado pelo servidor.
        var items = Array.prototype.map.call(list.querySelectorAll(".image-card"), function (el) {
            var hidden = el.querySelector('input[name="existing_images[]"]');
            var img = el.querySelector(".image-card-thumb");
            return { type: "existing", value: hidden ? hidden.value : "", previewSrc: img ? img.src : "" };
        });

        function showWarning(msg) {
            if (!warning) return;
            warning.textContent = msg || "";
            warning.hidden = !msg;
        }

        function buildCard(item, index, total) {
            var card = document.createElement("div");
            card.className = "image-card";

            var img = document.createElement("img");
            img.className = "image-card-thumb";
            img.src = item.previewSrc || "";
            img.alt = "";
            card.appendChild(img);

            var badge = document.createElement("span");
            badge.className = "image-card-badge";
            badge.textContent = index === 0 ? "Principal" : String(index + 1);
            card.appendChild(badge);

            var actions = document.createElement("div");
            actions.className = "image-card-actions";

            var up = document.createElement("button");
            up.type = "button";
            up.className = "image-card-btn";
            up.setAttribute("data-action", "up");
            up.setAttribute("aria-label", "Mover para cima");
            up.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 15 6-6 6 6"/></svg>';
            if (index === 0) up.disabled = true;
            actions.appendChild(up);

            var down = document.createElement("button");
            down.type = "button";
            down.className = "image-card-btn";
            down.setAttribute("data-action", "down");
            down.setAttribute("aria-label", "Mover para baixo");
            down.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>';
            if (index === total - 1) down.disabled = true;
            actions.appendChild(down);

            var remove = document.createElement("button");
            remove.type = "button";
            remove.className = "image-card-btn image-card-btn-danger";
            remove.setAttribute("data-action", "remove");
            remove.setAttribute("aria-label", "Remover imagem");
            remove.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6 6 18"/></svg>';
            actions.appendChild(remove);

            card.appendChild(actions);

            if (item.type === "existing") {
                var hidden = document.createElement("input");
                hidden.type = "hidden";
                hidden.name = "existing_images[]";
                hidden.value = item.value;
                card.appendChild(hidden);
            } else if (item.type === "new" && item.kind === "url") {
                var hiddenUrl = document.createElement("input");
                hiddenUrl.type = "hidden";
                hiddenUrl.name = "new_urls[]";
                hiddenUrl.value = item.value;
                card.appendChild(hiddenUrl);
            }

            return card;
        }

        function syncFileInput() {
            if (typeof DataTransfer === "undefined") return;
            var dt = new DataTransfer();
            items.forEach(function (item) {
                if (item.type === "new" && item.kind === "file") dt.items.add(item.file);
            });
            fileInput.files = dt.files;
        }

        function syncOrderField() {
            orderInput.value = items.map(function (item) {
                if (item.type === "existing") return "e";
                return item.kind === "url" ? "u" : "n";
            }).join(",");
        }

        function updateAddControlsState() {
            // Nota: nunca desativar o fileInput ("disabled") — inputs de ficheiro
            // desativados são excluídos do envio do formulário pelo browser, o que
            // apagaria silenciosamente os ficheiros já escolhidos ao atingir o máximo.
            var atMax = items.length >= maxImages;
            urlInput.disabled = atMax;
            urlAddBtn.disabled = atMax;
            manager.classList.toggle("image-manager-max", atMax);
        }

        function render() {
            list.innerHTML = "";
            items.forEach(function (item, i) {
                list.appendChild(buildCard(item, i, items.length));
            });
            syncFileInput();
            syncOrderField();
            updateAddControlsState();
        }

        list.addEventListener("click", function (e) {
            var btn = e.target.closest(".image-card-btn");
            if (!btn || btn.disabled) return;
            var card = btn.closest(".image-card");
            var index = Array.prototype.indexOf.call(list.children, card);
            if (index === -1) return;
            var action = btn.getAttribute("data-action");
            if (action === "remove") {
                items.splice(index, 1);
                showWarning("");
            } else if (action === "up" && index > 0) {
                var tmp = items[index - 1]; items[index - 1] = items[index]; items[index] = tmp;
            } else if (action === "down" && index < items.length - 1) {
                var tmp2 = items[index + 1]; items[index + 1] = items[index]; items[index] = tmp2;
            }
            render();
        });

        fileInput.addEventListener("click", function (e) {
            if (items.length >= maxImages) {
                e.preventDefault();
                showWarning("Só pode ter até " + maxImages + " imagens por artigo.");
            }
        });

        fileInput.addEventListener("change", function () {
            var picked = Array.prototype.slice.call(fileInput.files || []);
            var overflow = false;
            picked.forEach(function (file) {
                if (items.length >= maxImages) { overflow = true; return; }
                items.push({ type: "new", kind: "file", file: file, previewSrc: URL.createObjectURL(file) });
            });
            showWarning(overflow ? "Só pode ter até " + maxImages + " imagens por artigo." : "");
            render();
        });

        urlAddBtn.addEventListener("click", function () {
            var val = (urlInput.value || "").trim();
            if (!val) return;
            if (!/^https?:\/\//i.test(val)) {
                showWarning("O URL da imagem deve começar por http:// ou https://");
                return;
            }
            if (items.length >= maxImages) {
                showWarning("Só pode ter até " + maxImages + " imagens por artigo.");
                return;
            }
            items.push({ type: "new", kind: "url", value: val, previewSrc: val });
            urlInput.value = "";
            showWarning("");
            render();
        });

        render();
    })();

    // Checkout: mostrar os campos de morada só quando "Envio" está escolhido
    (function () {
        var form = document.querySelector(".checkout-form");
        if (!form) return;
        var shippingSection = document.getElementById("shipping-fields");
        var radios = form.querySelectorAll('input[name="fulfillment"]');

        function update() {
            var selected = form.querySelector('input[name="fulfillment"]:checked');
            shippingSection.hidden = !(selected && selected.value === "envio");
        }
        radios.forEach(function (r) { r.addEventListener("change", update); });
        update();
    })();

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
