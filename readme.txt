=== WooCommerce PagSeguro ===
Contributors: claudiosanches, Gabriel Reguly
Donate link: https://claudiosanches.com/doacoes/
Tags: woocommerce, pagseguro, payment
Requires at least: 4.0
Tested up to: 4.7
Stable tag: 2.12.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds PagSeguro gateway to the WooCommerce plugin

== Description ==

### Add PagSeguro gateway to WooCommerce ###

This plugin adds PagSeguro gateway to WooCommerce.

Please notice that WooCommerce must be installed and active.

= Contribute =

You can contribute to the source code in our [GitHub](https://github.com/claudiosmweb/woocommerce-pagseguro) page.

### Descrição em Português: ###

Adicione o PagSeguro como método de pagamento em sua loja WooCommerce.

[PagSeguro](https://pagseguro.uol.com.br/) é um método de pagamento brasileiro desenvolvido pela UOL.

O plugin WooCommerce PagSeguro foi desenvolvido sem nenhum incentivo do PagSeguro ou da UOL. Nenhum dos desenvolvedores deste plugin possuem vínculos com estas duas empresas.

Este plugin foi desenvolvido a partir da [documentação oficial do PagSeguro](https://pagseguro.uol.com.br/v2/guia-de-integracao/visao-geral.html) e utiliza a última versão da API de pagamentos.

Estão disponíveis as seguintes modalidades de pagamento:

- **Padrão:** Cliente é redirecionado ao PagSeguro para concluir a compra.
- **Lightbox:** Uma janela do PagSeguro é aberta na finalização para o cliente fazer o pagamento.
- **Transparente:** O cliente faz o pagamento direto no seu site sem precisar ir ao site do PagSeguro.

Além que é possível utilizar o novo [sandbox do PagSeguro](https://sandbox.pagseguro.uol.com.br/vendedor/configuracoes.html).

= Compatibilidade =

Compatível desde a versão 2.4.x até 3.0.x do WooCommerce.

Este plugin também é compatível com o [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/), desta forma é possível enviar os campos de "CPF", "número do endereço" e "bairro" (para o Checkout Transparente é obrigatório o uso deste plugin).

= Instalação =

Confira o nosso guia de instalação e configuração do PagSeguro na aba [Installation](http://wordpress.org/plugins/woocommerce-pagseguro/installation/).

= Integração =

Este plugin funciona perfeitamente em conjunto com:

* [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/).
* [WooCommerce Multilingual](https://wordpress.org/plugins/woocommerce-multilingual/).

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* A nossa sessão de [FAQ](http://wordpress.org/plugins/woocommerce-pagseguro/faq/).
* Utilizando o nosso [fórum no Github](https://github.com/claudiosmweb/woocommerce-pagseguro).
* Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/woocommerce-pagseguro).

= Colaborar =

Você pode contribuir com código-fonte em nossa página no [GitHub](https://github.com/claudiosmweb/woocommerce-pagseguro).

= Agradecimentos =

* [Leandro Matos](http://is-uz.com/) ajudou com o layout e os ícones do Checkout Transparente.

== Installation ==

* Upload plugin files to your plugins folder, or install using WordPress built-in Add New Plugin installer;
* Activate the plugin;
* Navigate to WooCommerce -> Settings -> Payment Gateways, choose PagSeguro and fill in your PagSeguro Email and Token.

### Instalação e configuração em Português: ###

= Instalação do plugin: =

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
* Ative o plugin.

= Requerimentos: =

É necessário possuir uma conta no [PagSeguro](http://pagseguro.uol.com.br/) e ter instalado o [WooCommerce](http://wordpress.org/plugins/woocommerce/).

= Configurações no PagSeguro: =

No PagSeguro é necessário desativar a opção de "Pagamento via Formulário HTML", você pode fazer isso em "Preferências" > "[Integrações](https://pagseguro.uol.com.br/preferencias/integracoes.jhtml)".

Apenas com isso já é possível receber os pagamentos e fazer o retorno automático de dados.

<blockquote>Atenção: Não é necessário configurar qualquer URL em "Página de redirecionamento" ou "Notificação de transação", pois o plugin é capaz de comunicar o PagSeguro pela API quais URLs devem ser utilizadas para cada situação.</blockquote>

= Configurações do Plugin: =

Com o plugin instalado acesse o admin do WordPress e entre em "WooCommerce" > "Configurações" > "Finalizar compra" > "PagSeguro".

Habilite o PagSeguro, adicione o seu e-mail e o token do PagSeguro. O token é utilizado para gerar os pagamentos e fazer o retorno de dados.

Você pode conseguir um token no PagSeguro em "Integrações" > "[Token de Segurança](https://pagseguro.uol.com.br/integracao/token-de-seguranca.jhtml)".

É possível escolher entre três opções de pagamento que são:

- **Checkout no PagSeguro (padrão):** O cliente e redirecionado para o site do PagSeguro
- **Checkout em Lighbox:** O cliente permanece no seu site é aberto um Lightbox do PagSeguro onde o cliente fará o pagamento
- **Checkout Transparente:** O cliente faz o pagamento direto em seu site na página de finalizar pedido utilizando a API do PagSeguro.

Você ainda pode definir o comportamento da integração utilizando as opções:

- **Enviar apenas o total do pedido:** Permite enviar apenas o total do pedido no lugar da lista de itens, esta opção deve ser utilizada apenas quando o total do pedido no WooCommerce esta ficando diferente do total no PagSeguro.
- **Prefixo de pedido:** Esta opção é útil quando você esta utilizando a mesma conta do PagSeguro em várias lojas e com isso você pode diferenciar os pagamentos pelo prefixo.

= Checkout Transparente =

Para utilizar o checkout transparente é necessário utilizar o plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/).

Com o **WooCommerce Extra Checkout Fields for Brazil** instalado e ativado você deve ir até "WooCommerce > Campos do Checkout" e configurar a opção "Exibir Tipo de Pessoa" como "Pessoa Física apenas".

Isto é necessário porque é obrigatório o envio de CPF para o PagSeguro, além de que o PagSeguro aceita apenas CPF.

Note que é necessário aprovação do PagSeguro para utilizar o Checkout Transparente, saiba mais em "[Como receber pagamentos pelo PagSeguro](https://pagseguro.uol.com.br/receba-pagamentos.jhtml)".

Pronto, sua loja já pode receber pagamentos pelo PagSeguro.

== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

= What is needed to use this plugin? =

* WooCommerce version 2.2 or latter installed and active.
* Only one account on [PagSeguro](http://pagseguro.uol.com.br/ "PagSeguro").

### FAQ em Português: ###

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin WooCommerce 2.2 ou superior.
* Possuir uma conta no PagSeguro.
* Gerar um token de segurança no PagSeguro.
* Desativar a opção "Pagamento via Formulário HTML" em integrações na página do PagSeguro.

= PagSeguro recebe pagamentos de quais países? =

No momento o PagSeguro recebe pagamentos apenas do Brasil.

Configuramos o plugin para receber pagamentos apenas de usuários que selecionarem o Brasil nas informações de pagamento durante o checkout.

= Quais são os meios de pagamento que o plugin aceita? =

São aceitos todos os meios de pagamentos que o PagSeguro disponibiliza, entretanto você precisa ativa-los na sua conta.

Confira os [meios de pagamento e parcelamento](https://pagseguro.uol.com.br/para_voce/meios_de_pagamento_e_parcelamento.jhtml#rmcl).

= Como que plugin faz integração com PagSeguro? =

Fazemos a integração baseada na documentação oficial do PagSeguro que pode ser encontrada nos "[guias de integração](https://pagseguro.uol.com.br/receba-pagamentos.jhtml)" utilizando a última versão da API de pagamentos.

= Instalei o plugin, mas a opção de pagamento do PagSeguro some durante o checkout. O que fiz de errado? =

Você esqueceu de selecionar o Brasil durante o cadastro no checkout.

A opção de pagamento pelo PagSeguro funciona apenas com o Brasil.

= É possível enviar os dados de "Número", "Bairro" e "CPF" para o PagSeguro? =

Sim é possível, basta utilizar o plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/).

= O pedido foi pago e ficou com o status de "processando" e não como "concluído", isto esta certo? =

Sim, esta certo e significa que o plugin esta trabalhando como deveria.

Todo gateway de pagamentos no WooCommerce deve mudar o status do pedido para "processando" no momento que é confirmado o pagamento e nunca deve ser alterado sozinho para "concluído", pois o pedido deve ir apenas para o status "concluído" após ele ter sido entregue.

Para produtos baixáveis a configuração padrão do WooCommerce é permitir o acesso apenas quando o pedido tem o status "concluído", entretanto nas configurações do WooCommerce na aba *Produtos* é possível ativar a opção **"Conceder acesso para download do produto após o pagamento"** e assim liberar o download quando o status do pedido esta como "processando".

= Ao tentar finalizar a compra aparece a mensagem "PagSeguro: Um erro ocorreu ao processar o seu pagamento, por favor, tente novamente ou entre em contato para obter ajuda." o que fazer? =

Esta mensagem geralmente aparece por causa que não foi configurado um **Token válido**.
Gere um novo Token no PagSeguro em "Preferências" > "[Integrações](https://pagseguro.uol.com.br/preferencias/integracoes.jhtml)" e adicione ele nas configurações do plugin.

Outro erro comum é gerar um token e cadastrar nas configurações do plugin um e-mail que não é o proprietário do token, então tenha certeza que estes dados estão realmente corretos!

Note que caso você esteja utilizando a opção de **sandbox** é necessário usar um e-mail e token de testes que podem ser encontrados em "[PagSeguro Sandbox > Dados de Teste](https://sandbox.pagseguro.uol.com.br/vendedor/configuracoes.html)".

Se você tem certeza que o Token e Login estão corretos você deve acessar a página "WooCommerce > Status do Sistema" e verificar se **fsockopen** e **cURL** estão ativos. É necessário procurar ajuda do seu provedor de hospedagem caso você tenha o **fsockopen** e/ou o **cURL** desativados.

Para quem estiver utilizando o **Checkout Transparente** é obrigatório o uso do plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/) para enviar o CPF ao PagSeguro, caso o contrário será impossível de finalizar o pedido, veja no [guia de instalação](http://wordpress.org/plugins/woocommerce-pagseguro/installation/) como fazer isso.

Por último é possível ativar a opção de **Log de depuração** nas configurações do plugin e tentar novamente fechar um pedido (você deve tentar fechar um pedido para que o log será gerado e o erro gravado nele).
Com o log é possível saber exatamente o que esta dando de errado com a sua instalação.

Caso você não entenda o conteúdo do log não tem problema, você pode me abrir um [tópico no fórum do plugin](https://wordpress.org/support/plugin/woocommerce-pagseguro#postform) com o link do log (utilize o [pastebin.com](http://pastebin.com) ou o [gist.github.com](http://gist.github.com) para salvar o conteúdo do log).

= O status do pedido não é alterado automaticamente? =

Sim, o status é alterado automaticamente usando a API de notificações de mudança de status do PagSeguro.

Caso os status dos seus pedidos não estiverem sendo alterados siga o tutorial do PagSeguro:

* [Não recebi o POST do retorno automático. O que devo fazer?](https://pagseguro.uol.com.br/atendimento/perguntas_frequentes/nao-recebi-o-post-com-retorno-automatico-o-que-devo-fazer.jhtml)

A seguir uma lista de ferramentas que podem estar bloqueando as notificações do PagSeguro:

* Site com CloudFlare, pois por padrão serão bloqueadas quaisquer comunicações de outros servidores com o seu. É possível resolver isso desbloqueando a lista de IPs do PagSeguro.
* Plugin de segurança como o "iThemes Security" com a opção para adicionar a lista do HackRepair.com no .htaccess do site. Acontece que o user-agent do PagSeguro esta no meio da lista e vai bloquear qualquer comunicação. Você pode remover isso da lista, basta encontrar onde bloquea o user-agent "jakarta" e deletar ou criar uma regra para aceitar os IPs do PagSeguro).
* `mod_security` habilitado, neste caso vai acontecer igual com o CloudFlare bloqueando qualquer comunicação de outros servidores com o seu. Como solução você pode desativar ou permitir os IPs do PagSeguro.

= Funciona com o Lightbox do PagSeguro? =

Sim, basta ativar esta nas opções do plugin.

= Funciona com o checkout transparente do PagSeguro? =

Sim, funciona. Você deve ativar nas opções do plugin.
Note que é necessário aprovação do PagSeguro para utilizar o Checkout Transparente, saiba mais em "[Como receber pagamentos pelo PagSeguro](https://pagseguro.uol.com.br/receba-pagamentos.jhtml)".

= Funciona com o Sandbox do PagSeguro? =

Sim, funciona e basta você ativar isso nas opções do plugin, além de configurar o seu [e-mail e token de testes](https://sandbox.pagseguro.uol.com.br/vendedor/configuracoes.html)".

= O total do pedido no WooCommerce é diferente do enviado para o PagSeguro, como eu resolvo isso? =

Caso você tenha este problema, basta marcar ativar a opção **Enviar apenas o total do pedido** na página de configurações do plugin.

= Quais URLs eu devo usar para configurar "Notificação de transação" e "Página de redirecionamento"? =

Não é necessário configurar qualquer URL para "Notificação de transação" ou para "Página de redirecionamento", o plugin já diz para o PagSeguro quais URLs serão utilizadas.

= Mais dúvidas relacionadas ao funcionamento do plugin? =

Por favor, caso você tenha algum problema com o funcionamento do plugin, [abra um tópico no fórum do plugin](https://wordpress.org/support/plugin/woocommerce-pagseguro#postform) com o link arquivo de log (ative ele nas opções do plugin e tente fazer uma compra, depois vá até WooCommerce > Status do Sistema, selecione o log do *pagseguro* e copie os dados, depois crie um link usando o [pastebin.com](http://pastebin.com) ou o [gist.github.com](http://gist.github.com)), desta forma fica mais rápido para fazer o diagnóstico.

== Screenshots ==

1. Configurações do plugin.
2. Método de pagamento na página de finalizar o pedido.
3. Exemplo do Lightbox funcionando com o Sandbox do PagSeguro.
4. Pagamento com cartão de crédito usando o Checkout Transparente.
5. Pagamento com debito online usando o Checkout Transparente.
6. Pagamento com boleto bancário usando o Checkout Transparente.

== Changelog ==

= 2.12.5 - 2017/05/11 =

* Corrigido valor total das parcelas do cartão de crédito no checkout transparente, o valor tinha parado de ser atualizado no WooCommerce 3.0.

= 2.12.4 - 2017/04/12 =

* Corrigido icones no checkout transparente.

= 2.12.3 - 2017/04/10 =

* Corrigido `ndash` que aparecia no nome dos itens listados no PagSeguro.

= 2.12.2 - 2017/04/07 =

* Adicionado suporte ao novo sistema de logs do WooCommerce 3.0, assim permitindo que seja utilizado sistema de logs personalizados.
* Adicionado validação e higienização no código de transação do PagSeguro antes de salvar.

= 2.12.1 - 2017/04/04 =

* Correção dos títulos dos campos personalizados salvos ao fazer um pedido.

= 2.12.0 - 2017/04/03 =

* Adicionado suporte ao WooCommerce 3.0.
* Alterado o tipo dos campos para `tel` no Checkout Transparente. (Possível com a ajuda de [Thiago Guimarães](https://github.com/thiagogsr)).
* Correção nas máscaras do campos devido a mudança do plugin no [woocommerce-extra-checkout-fields-for-brazil](https://github.com/claudiosanches/woocommerce-extra-checkout-fields-for-brazil/pull/49). (Possível com a ajuda de [Thiago Guimarães](https://github.com/thiagogsr)).

= 2.11.5 - 2017/01/17 =

* Adicionada nota dizendo que o pedido esta sendo feito no Brasil durante o Checkout Transparente.

= 2.11.4 - 2016/11/30 =

* Adicionada nova mensagem de erro quando utilizado o mesmo e-mail para realizar pagamentos com o mesmo e-mail da conta do recebedor.
* Removida opção obsoleta de déposito bancário pelo Bradesco.
* Corrigido links do Sandbox na página de administração.
* Adicionada modificação para enviar nome da empresa quando utilizado CNPJ.

= 2.11.3 - 2016/03/17 =

* Corrigida finalização com produtos gratuitos.

= 2.11.2 - 2015/12/30 =

* Correção de erro fatal ao tentar finalizar pedido usando CNPJ.

= 2.11.1 - 2015/12/24 =

* Corrigido campo bloqueado de telefone no checkout transparente.

= 2.11.0 - 2015/12/21 =

* Adicionado suporte a compras com CNPJ.
* Melhorado o checkout transparente quando usado o tema Storefront.
* Adicionada opções para token e e-mail de sandbox.

== Upgrade Notice ==

= 2.12.5 =

* Corrigido valor total das parcelas do cartão de crédito no checkout transparente, o valor tinha parado de ser atualizado no WooCommerce 3.0.
