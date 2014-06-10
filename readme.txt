=== WooCommerce PagSeguro ===
Contributors: claudiosanches, Gabriel Reguly
Donate link: http://claudiosmweb.com/doacoes/
Tags: woocommerce, pagseguro, payment
Requires at least: 3.5
Tested up to: 3.9.1
Stable tag: 2.4.0
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

Este plugin desenvolvido a partir da [documentação oficial do PagSeguro](https://pagseguro.uol.com.br/v2/guia-de-integracao/visao-geral.html) e utiliza a última versão da API de pagamentos.

Estão disponíveis as seguintes modalidades de pagamento:

* Padrão - Cliente é redirecionado ao PagSeguro para concluir a compra.
* Lightbox - Uma janela do PagSeguro é aberta na finalização para o cliente fazer o pagamento.
* Transparente - Em breve!

Além que é possível utilizar o novo [sandbox do PagSeguro](https://sandbox.pagseguro.uol.com.br/dados-de-teste.html).

= Compatibilidade =

Compatível com as versões 2.0.x e 2.1.x do WooCommerce.

= Instalação =

Confira o nosso guia de instalação e configuração do PagSeguro na aba [Installation](http://wordpress.org/extend/plugins/woocommerce-pagseguro/installation/).

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* A nossa sessão de [FAQ](http://wordpress.org/extend/plugins/woocommerce-pagseguro/faq/).
* Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/woocommerce-pagseguro) (apenas em inglês).
* Ou entre em contato com os desenvolvedores do plugin em nossa [página](http://claudiosmweb.com/plugins/pagseguro-para-woocommerce/).

= Coloborar =

Você pode contribuir com código-fonte em nossa página no [GitHub](https://github.com/claudiosmweb/woocommerce-pagseguro).

== Installation ==

* Upload plugin files to your plugins folder, or install using WordPress built-in Add New Plugin installer;
* Activate the plugin;
* Navigate to WooCommerce -> Settings -> Payment Gateways, choose PagSeguro and fill in your PagSeguro Email and Token.

### Instalação e configuração em Português: ###

= Instalação do plugin: =

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
* Ative o plugin.

= Requerimentos: =

É necessário possuir uma conta no [PagSeguro](http://pagseguro.uol.com.br/) e ter instalado o [WooCommerce](http://wordpress.org/extend/plugins/woocommerce/).

= Configurações no PagSeguro: =

**Configuração Segura (recomendado):**

No PagSeguro basta aceitar receber pagamentos apenas pela **API**.

Você deve ativar esta opção em "Integrações" > "[Pagamentos via API](https://pagseguro.uol.com.br/integracao/pagamentos-via-api.jhtml)".

Apenas com isso já é possível receber os pagamentos e fazer o retorno automático de dados.

**Configuração menos segura (não recomendado):**

Para esta opção você deve deixar a opção de **Pagamentos via API** desativada e configurar a página de redirecionamento.

Desta forma você deve ir até "Integrações" > "[Página de redirecionamento](https://pagseguro.uol.com.br/integracao/pagina-de-redirecionamento.jhtml)":

Ative a opção de "Página fixa de redirecionamento" e configure ela como por exemplo:

	http://seusite.com/finalizar-compra/pedido-recebido/

Habilite também o retorno automático de dados;
Deve ser ativado em "Integrações" > "[Retorno automático de dados](https://pagseguro.uol.com.br/integracao/retorno-automatico-de-dados.jhtml)".

= Configurações do Plugin: =

Com o plugin instalado acesse o admin do WordPress e entre em "WooCommerce" > "Configurações" > "Finalizar compra" > "PagSeguro".

Habilite o PagSeguro, adicione o seu e-mail e o token do PagSeguro. O token é utilizado para gerar os pagamentos e fazer o retorno de dados.

Você pode conseguir um token no PagSeguro em "Integrações" > "[Token de Segurança](https://pagseguro.uol.com.br/integracao/token-de-seguranca.jhtml)".

= Configurações no WooCommerce =

No WooCommerce 2.0 ou superior existe uma opção para cancelar a compra e liberar o estoque depois de alguns minutos.

Esta opção não funciona muito bem com o PagSeguro, pois pagamentos por boleto bancário pode demorar até 48 horas para serem validados.

Para corrigir isso é necessário ir em "WooCommerce" > "Configurações" > "Produtos" > "Inventário" e limpar (deixe em branco) a opção **Manter Estoque (minutos)**.

Pronto, sua loja já pode receber pagamentos pelo PagSeguro.

== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

= What is needed to use this plugin? =

* WooCommerce version 2.0 or latter installed and active.
* Only one account on [PagSeguro](http://pagseguro.uol.com.br/ "PagSeguro").

### FAQ em Português: ###

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin WooCommerce 2.0 ou superior.
* Possuir uma conta no PagSeguro.
* Gerar um token de segurança no PagSeguro.
* Utilizar uma das duas opções de configuração no PagSeguro (veja elas no guia de [instalação do plugin](http://wordpress.org/extend/plugins/woocommerce-pagseguro/installation/)).
* Desativar os pagamentos via API.

= Como funciona o PagSeguro? =

* Saiba mais em [PagSeguro - Como funciona](https://pagseguro.uol.com.br/para_seu_negocio/como_funciona.jhtml).
* Ou acesse a [FAQ do PagSeguro](https://pagseguro.uol.com.br/atendimento/perguntas_frequentes.jhtml).

= PagSeguro recebe pagamentos de quais países? =

No momento o PagSeguro recebe pagamentos apenas do Brasil.

Configuramos o plugin para receber pagamentos apenas de usuários que selecionarem o Brasil nas informações de pagamento durante o checkout.

= Quais são os meios de pagamento que o plugin aceita? =

São aceitos todos os meios de pagamentos que o PagSeguro disponibiliza.
Entretanto você precisa ativa-los na sua conta no PagSeguro.

Confira os meios de pagamento em [PagSeguro - Meios de Pagamento e Parcelamento](https://pagseguro.uol.com.br/para_voce/meios_de_pagamento_e_parcelamento.jhtml#rmcl).

= Quais são as taxas de transações que o PagSeguro cobra? =

Para estas informações consulte: [PagSeguro - Taxas e Tarifas](https://pagseguro.uol.com.br/taxas_e_tarifas.jhtml).

= Quais são os limites de recebimento do PagSeguro? =

Consulte: [PagSeguro - Tabela de Limites](https://pagseguro.uol.com.br/account/limits.jhtml).

= Como que plugin faz integração com PagSeguro? =

Fazemos a integração baseada na documentação oficial do PagSeguro que pode ser encontrada em "[Guia de integração - PagSeguro](https://pagseguro.uol.com.br/v2/guia-de-integracao/visao-geral.html)" utilizando a última versão da API de pagamentos.

= Instalei o plugin, mas a opção de pagamento do PagSeguro some durante o checkout. O que fiz de errado? =

Você esqueceu de selecionar o Brasil durante o cadastro no checkout.
A opção de pagamento pelo PagSeguro funciona apenas com o Brasil.

= É possível enviar os dados de "Número", "Bairro" e "CPF" para o PagSeguro? =

Sim é possível, basta utilizar o plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/extend/plugins/woocommerce-extra-checkout-fields-for-brazil/).

= Ao tentar finalizar a compra aparece a mensagem "PagSeguro: Um erro ocorreu ao processar o seu pagamento, por favor, tente novamente ou entre em contato para obter ajuda." o que fazer? =

Esta mensagem geralmente aparece por causa que não foi configurado um **Token válido**.  
Gere um novo Token no PagSeguro em "Integrações" > "[Token de Segurança](https://pagseguro.uol.com.br/integracao/token-de-seguranca.jhtml)" e adicione ele nas configurações do plugin.

Outro erro comum é gerar um token e cadastrar nas configurações do plugin um e-mail que não é o proprietário do token, então tenha certeza que estes dados estão realmente corretos!

Note que caso você esteja utilizando a opção de **sandbox** é necessário usar um e-mail e token de teste que podem ser encontrados em "[PagSeguro Sandbox > Dados de Teste](https://sandbox.pagseguro.uol.com.br/dados-de-teste.html)".

Caso o erro persista mesmo com o novo Token, você deve ativar a opção **Log de depuração** nas configurações do plugin e tentar novamente fechar uma compra para que desta forma seja gerado o log.  
Me envie o link por [e-mail](http://claudiosmweb.com/contato/), desta forma posso te ajudar a resolver o problema.

= A compra é cancelada após alguns minutos, mesmo com o pedido sendo pago, como resolvo isso? =

Para resolver este problema vá até "WooCommerce" > "Configurações" > "Produtos" > "Inventário" e limpe (deixe em branco) a opção **Manter Estoque (minutos)**.

= Funciona com o Lightbox do PagSeguro? =

Sim, basta ativar esta nas opções do plugin.

= Funciona com o checkout transparente do PagSeguro? =

No momento não, estaremos implementando isso em futuras versões do plugin, aguarde!

= Funciona com o Sandbox do PagSeguro? =

Sim, funciona e basta você ativar isso nas opções do plugin, além de configurar o seu token de desenvolvedor.

Para conseguir o token de desenvolver você deve acessar "[PagSeguro Sandbox > Dados de Teste](https://sandbox.pagseguro.uol.com.br/dados-de-teste.html)".

= Mais dúvidas relacionadas ao funcionamento do plugin? =

Entre em contato [clicando aqui](http://claudiosmweb.com/plugins/pagseguro-para-woocommerce/).

Por favor, caso você tenha algum problema com o funcionamento do plugin, envie o log (ative ele nas opções do plugin e tente fazer uma compra, ele vai ficar dentro da pasta wp-content/plugins/woocommerce/logs/) usando o `pastebin.com` ou o `gist.github.com`, desta forma fica mais rápido para fazer o diagnóstico.

== Screenshots ==

1. Plugin settings page.
2. WooCommerce payment methods in checkout page.
2. Lightbox in sandbox environment example.

== Changelog ==

= 2.4.0 - 10/06/2014 =

* Correções nas mensagens do log para a criação de tokens de pagamento.
* Adicionada opção de ambiente sandbox.

= 2.3.1 - 24/05/2014 =

* Melhoria nos status do pedido, agora ao gerar um boleto o pedido é alterado para "aguardando".
* Modificado o botão "Finalizar pedido", com o WooCommerce 2.1 ou superior vai mostrar a mensagem "Realizar pagamento".

= 2.3.0 - 02/04/2014 =

* Adicionada opção para selecionar pagamento direto com redirecionamento ou pelo Lightbox do PagSeguro.

= 2.2.1 - 06/12/2013 =

* Melhoria na compatibilidade com o WooCommerce 2.1.

= 2.2.0 - 04/12/2013 =

* Corrigido padrões de código.
* Removida compatibilidade com versões 1.6.x ou inferiores do WooCommerce.
* Adicionada compatibilidade com WooCommerce 2.1 ou superior.

= 2.1.1 - 03/09/2013 =

* Adicionada mensagem sobre DDD errado nas mensagens de erro do PagSeguro.
* Correção da verificação do IPN.

= 2.1.0 - 29/08/2013 =

* Adicionada função para tratar as mensagens de erro do PagSeguro para CPF, CEP e número de telefone.

= 2.0.3 - 22/08/2013 =

* Correção da alteração de status pelo pela notificação de pagamento do PagSeguro.

= 2.0.2 - 22/08/2013 =

* Corrigido o erro causado com números de telefone sem DDD.

= 2.0.1 - 19/08/2013 =

* Removida a obrigatoriedade de enviar os campos de endereço.

= 2.0.0 - 17/08/2013 =

* Adicionadas as novas APIs de pagamentos e notificações do PagSeguro.
* Removidas as APIs antigas de pagamento e notificações do PagSeguro.
* Melhoria nos status de pagamento.
* Melhorias na notificações sobre compras em disputas ou que tiveram o pagamento devolvido.

= 1.6.1 - 14/08/2013 =

* Melhoria no JavaScript inline no formulário de checkout.

= 1.6.0 - 26/07/2013 =

* Adicionado o filtro `woocommerce_pagseguro_icon` para troca do ícone do método de pagamento.
* Melhoria no filtro `woocommerce_pagseguro_args`, agora ele aceita o objeto `WC_Order` no lugar do ID.
* Melhoria nas opções do plugin.
* Melhoria nas mensagens de status do pedido.

= 1.5.0 - 24/06/2013 =

* Adicionado link de `Configurações` na página de plugins.
* Melhorias no código.
* Adicionado suporte para WooCommerce 2.1.
* Adicionado o ID da compra no filtro `woocommerce_pagseguro_args`.

= 1.4.0 - 02/04/2013 =

* Correção do retorno automático de dados na versão 2.0.0 ou superior do WooCommerce.

= 1.3.4 - 06/03/2013 =

* Corrigida a compatibilidade com WooCommerce 2.0.0 ou mais recente.

= 1.3.3 - 08/02/2013 =

* Corrigido o hook responsavel por salvar as opções para a versão 2.0 RC do WooCommerce.

= 1.3.2 - 08/02/2013 =

* Plugin corrigido para a versão 2.0 do WooCommerce.

= 1.3.1 - 08/12/2012 =

* Melhoria no método que atualiza o status do pedido.
* Correção da quantidade de caracteres das descrição dos produtos no PagSeguro.

= 1.3.0 - 30/11/2012 =

* Adicionada opção para logs de erro.
* Adiciona opção para validar ou não endereço (quando ativo força cliente a informar os dados corretamente e ir direto para a página de pagamento do PagSeguro).

= 1.2.2 - 19/11/2012 =

* Corrigido problema com cupons de desconto (descontos no carrinho).

= 1.2.1 =

* Corrigido o problema de produto com a descrição/nome muito grande no PagSeguro.

= 1.2.0 =

* Removida a classe do retorno automático que usava cURL em favor da função wp_remote_post().
* Otimizado o retorno automático de dados.

= 1.1.1 =

* Tradução revisada.
* Melhorada a integração de retorno automático para o status de "Aguardando pagamento".

= 1.1.0 =

* Adicionado retorno automático de dados.
* Melhorado o sistema de notificações.
* Adicionada classe para validar o retorno automático de dados.
* Adicionado campo de token nas configurações do plugin (necessário para o retorno automático).

= 1.0.2 =

* Adicionada função para abater estoque automáticamente quando o cliente vai para o PagSeguro.

= 1.0.1 =

* Correção para impedir que o portal de pagamento seja habilitado se a conta de email do PagSeguro estiver em branco

= 1.0.0 =

* Versão incial do plugin.

== Upgrade Notice ==

= 2.3.1 =

* Melhoria nos status do pedido, agora ao gerar um boleto o pedido é alterado para "aguardando".
* Modificado o botão "Finalizar pedido", com o WooCommerce 2.1 ou superior vai mostrar a mensagem "Realizar pagamento".

== License ==

WooCommerce PagSeguro is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

WooCommerce PagSeguro is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with WooCommerce PagSeguro. If not, see <http://www.gnu.org/licenses/>.
