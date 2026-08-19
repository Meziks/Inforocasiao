<?php $biz = Seo::BIZ; ?>
<section class="container admin-section narrow">
    <h1>Termos de Utilização</h1>
    <p class="muted">Última atualização: <?= date('d/m/Y') ?></p>

    <div class="alert alert-error" style="margin: 18px 0;">
        <strong>Rascunho de trabalho.</strong> Este texto é uma base de partida e ainda não foi revisto por
        um advogado ou contabilista. Não deve ser considerado juridicamente válido antes dessa revisão —
        em particular as cláusulas de garantia, devolução e resolução de litígios.
    </div>

    <div class="card">
        <h3 class="form-section-title">1. Identificação</h3>
        <p>
            <?= e($biz['name']) ?>, com sede em <?= e($biz['street']) ?>, <?= e($biz['postal']) ?> <?= e($biz['city']) ?>,
            NIF 192115707, doravante "a Loja".
        </p>

        <h3 class="form-section-title">2. Objeto</h3>
        <p>
            Estes termos regulam a utilização do site e a compra de artigos (novos, usados e recondicionados)
            e serviços de reparação disponibilizados pela Loja.
        </p>

        <h3 class="form-section-title">3. Preços e IVA</h3>
        <p>Todos os preços apresentados incluem IVA à taxa legal em vigor.</p>

        <h3 class="form-section-title">4. Encomendas e pagamento</h3>
        <p>
            As formas de pagamento aceites são apresentadas no processo de encomenda. A encomenda só se
            considera confirmada após a confirmação de pagamento ou, no caso de pagamento na loja, após
            confirmação da reserva pela Loja.
        </p>

        <h3 class="form-section-title">5. Entrega e levantamento</h3>
        <p>
            As encomendas podem ser levantadas na loja em Cucujães ou enviadas para Portugal continental, nas
            condições indicadas no processo de encomenda.
        </p>

        <h3 class="form-section-title">6. Direito de livre resolução</h3>
        <p>
            Nos termos da lei portuguesa (Decreto-Lei n.º 24/2014), o cliente tem direito a resolver o
            contrato sem necessidade de indicar o motivo, no prazo de 14 dias a contar da receção do artigo.
            Para exercer este direito, deve contactar a Loja através dos <a href="<?= e(url('/contactos')) ?>">contactos</a>
            disponíveis no site. O artigo deve ser devolvido em bom estado; os custos de devolução são
            suportados pelo cliente, salvo indicação em contrário.
        </p>

        <h3 class="form-section-title">7. Garantia</h3>
        <p>
            Os artigos novos, usados e recondicionados vendidos pela Loja beneficiam da garantia legal
            aplicável, nos termos do Decreto-Lei n.º 84/2021.
        </p>

        <h3 class="form-section-title">8. Contactos e reclamações</h3>
        <p>
            Para qualquer questão relacionada com estes termos, contacte-nos através da página de
            <a href="<?= e(url('/contactos')) ?>">Contactos</a>. Em caso de litígio de consumo, o cliente pode
            recorrer a uma entidade de resolução alternativa de litígios, nos termos da Lei n.º 144/2015.
        </p>
    </div>
</section>
