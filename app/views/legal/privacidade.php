<?php $biz = Seo::BIZ; ?>
<section class="container admin-section narrow">
    <h1>Política de Privacidade</h1>
    <p class="muted">Última atualização: <?= date('d/m/Y') ?></p>

    <div class="alert alert-error" style="margin: 18px 0;">
        <strong>Rascunho de trabalho.</strong> Este texto é uma base de partida e ainda não foi revisto por
        um advogado ou contabilista. Não deve ser considerado juridicamente válido antes dessa revisão —
        em particular o email de contacto ainda está por confirmar.
    </div>

    <div class="card">
        <h3 class="form-section-title">1. Responsável pelo tratamento</h3>
        <p>
            <?= e($biz['name']) ?>, com sede em <?= e($biz['street']) ?>, <?= e($biz['postal']) ?> <?= e($biz['city']) ?>,
            NIF 192115707. Contacto: <a href="tel:<?= e($biz['phone']) ?>"><?= e($biz['phone']) ?></a>.
        </p>

        <h3 class="form-section-title">2. Que dados recolhemos</h3>
        <p>Ao criar conta ou fazer uma encomenda, podemos recolher: nome, email, telefone, morada de entrega
            e histórico de encomendas. Não recolhemos dados de cartões de pagamento — esses são processados
            diretamente pelo prestador de serviços de pagamento, nunca pela Loja.</p>

        <h3 class="form-section-title">3. Para que usamos os seus dados</h3>
        <p>Para processar encomendas, emitir faturas, contactar sobre o estado da encomenda, e responder a
            pedidos de apoio. Só usamos os seus dados para outras finalidades (ex: newsletter) com o seu
            consentimento explícito.</p>

        <h3 class="form-section-title">4. Base legal</h3>
        <p>O tratamento assenta na execução do contrato de compra e venda, no cumprimento de obrigações
            legais (ex: faturação) e, quando aplicável, no seu consentimento.</p>

        <h3 class="form-section-title">5. Partilha de dados</h3>
        <p>Os seus dados podem ser partilhados com prestadores de serviços estritamente necessários ao
            funcionamento da Loja (ex: processamento de pagamentos, envio de encomendas, envio de emails,
            faturação), sempre com garantias de proteção de dados.</p>

        <h3 class="form-section-title">6. Quanto tempo guardamos os seus dados</h3>
        <p>Os dados de encomendas e faturação são conservados pelo prazo legal aplicável (geralmente 10 anos
            para efeitos fiscais). Os dados de conta são conservados enquanto a conta estiver ativa.</p>

        <h3 class="form-section-title">7. Os seus direitos</h3>
        <p>Nos termos do RGPD, tem direito a aceder, retificar, apagar ou pedir a portabilidade dos seus
            dados, bem como a opor-se ou limitar o tratamento. Pode exercer estes direitos contactando-nos
            através da página de <a href="<?= e(url('/contactos')) ?>">Contactos</a>. Tem também o direito de
            apresentar reclamação junto da CNPD (Comissão Nacional de Proteção de Dados).</p>

        <h3 class="form-section-title">8. Cookies</h3>
        <p>O site usa apenas os cookies estritamente necessários ao seu funcionamento (ex: manter a sessão
            de login). Não usamos cookies de publicidade ou de rastreio de terceiros.</p>
    </div>
</section>
