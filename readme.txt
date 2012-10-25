=== WooCommerce PagSeguro ===
Contributors: claudiosanches, Gabriel Reguly
Tags: ecommerce, e-commerce, commerce, wordpress ecommerce, checkout, payment, payment gateway
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds PagSeguro gateway to the WooCommerce plugin

== Description ==

### Add PagSeguro gateway to WooCommerce

This plugin adds PagSeguro gateway to WooCommerce.

Please notice that WooCommerce must be installed and active.

### Descrição em Português:

Adicione o PagSeguro como método de pagamento em sua loja WooCommerce.

Confira o nosso guia de instalação e configuração do PagSeguro na aba [Installation](http://wordpress.org/extend/plugins/woocommerce-pagseguro/installation/).

== Installation ==

= Plugin Install: =
* Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
* Activate the plugin
* Navigate to WooCommerce -> Settings -> Payment Gateways, choose PagSeguro and fill in your PagSeguro Email

### Instalação e configuração em Português:

= Instalação do plugin: =

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
* Ative o plugin.

= Requerimentos: =

É necessário possuir uma conta no [PagSeguro](http://pagseguro.uol.com.br/) e ter instalado o [WooCommerce](http://wordpress.org/extend/plugins/woocommerce/).

= Configurações no PagSeguro: =

É necessário configurar a página de redirecionamento no PagSeguro.
Você pode fazer isso em “Integrações” > “[Página de redirecionamento](https://pagseguro.uol.com.br/integracao/pagina-de-redirecionamento.jhtml)”:

Ative a opção de “Página fixa de redirecionamento” e configure ela como por exemplo:

    http://seusite.com/finalizar-compra/pedido-recebido/

No momento o plugin funciona apenas com a integração simples. Desta forma é necessário desativar os pagamentos via API.
Você pode fazer isso em “Integrações” > “[Pagamentos via API](https://pagseguro.uol.com.br/integracao/pagamentos-via-api.jhtml)”.

Com estas configurações a sua conta no PagSeguro estará pronta para receber os pagamentos de sua loja.

= Configurações do Plugin: =

Com o plugin instalado acesse o admin do WordPress e entre em “WooCommerce” > “Configurações” > “Portais de pagamento”  > “PagSeguro”.

Habilite o PagSeguro e adicione o seu e-mail.

Pronto, sua loja já pode receber pagamentos pelo PagSeguro.

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* A nossa sessão de [FAQ](http://wordpress.org/extend/plugins/woocommerce-pagseguro/faq/).
* Criando um tópico no [forúm de ajuda do WordPress](http://wordpress.org/support/plugin/woocommerce-pagseguro) (apenas em inglês).
* Ou entre em contato com os desenvolvedores do plugin em nossa [página](http://claudiosmweb.com/plugins/pagseguro-para-woocommerce/).

== License ==

This file is part of WooCommerce PagSeguro.
WooCommerce PagSeguro is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
WooCommerce PagSeguro is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Author Bio Box. If not, see <http://www.gnu.org/licenses/>.

== Frequently Asked Questions ==

= What is the plugin license? =
* This plugin is released under a GPL license.

= What is needed to use this plugin? =
* WooCommerce installed and active
* Only one account on [PagSeguro](http://pagseguro.uol.com.br/ "PagSeguro").

### FAQ em Português:

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin WooCommerce.
* Possuir uma conta no PagSeguro.

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

Para estas informações consulte a página do [PagSeguro - Taxas e Tarifas](https://pagseguro.uol.com.br/taxas_e_tarifas.jhtml).

= Quais são os limites de recebimento do PagSeguro? =

Consulte os limites em [PagSeguro - Tabela de Limites](https://pagseguro.uol.com.br/account/limits.jhtml).

= O plugin faz integração usando a API 2.0 do PagSeguro? =

No momento a integração é feita apenas de forma básica.
Isso significa que você poderá receber seus pagamentos pelo PagSeguro utilizando seu e-mail sem nenhum problema.
Entretanto é necessário dar baixa manualmente nas compras.

Pretendemos no futuro utilizar a API 2.0 e realizar as transações com o Token.
Sendo assim, será possível usufruir do retorno automático do PagSeguro.

= Instalei o plugin, mas a opção de pagamento pelo PagSeguro some durante o checkout. O que fiz de errado? =

Você esqueceu de selecionar o Brasil durante o cadastro no checkout.
A opção de pagamento pelo PagSeguro funciona apenas com o Brasil.

= Mais dúvidas relacionadas ao funcionamento do plugin? =

Entre em contato [clicando aqui](http://claudiosmweb.com/plugins/pagseguro-para-woocommerce/).

== Changelog ==

= 1.0.2 =
* EN: Added function to automatically cull stock when the customer goes to the PagSeguro.
* PT-BR: Adicionada função para abater estoque automáticamente quando o cliente vai para o PagSeguro.

= 1.0.1 =
* EN: Fix to avoid enabling the gateway if the PagSeguro email account is empty
* PT-BR: Correção para impedir que o portal de pagamento seja habilitado se a conta de email do PagSeguro estiver em branco

= 1.0 =
* EN: Initial plugin release.
* PT-BR: Versão incial do plugin.

== Upgrade Notice ==

= 1.0.2 =
* Added function that adds the stock

= 1.0.1 =
* Recommended/Recomendado

= 1.0 =
* Enjoy it.

== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png
