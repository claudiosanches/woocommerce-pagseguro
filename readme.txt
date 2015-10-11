=== WooCommerce PagSeguro ===
Contributors: claudiosanches, Gabriel Reguly
Donate link: http://claudiosmweb.com/doacoes/
Tags: woocommerce, pagseguro, payment
Requires at least: 4.0
Tested up to: 4.3
Stable tag: 2.10.3
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

Além que é possível utilizar o novo [sandbox do PagSeguro](https://sandbox.pagseguro.uol.com.br/comprador-de-testes.html).

= Compatibilidade =

Compatível com as versões 2.1.x, 2.2.x, 2.3.x e 2.4.x do WooCommerce.

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

= Coloborar =

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

No PagSeguro basta aceitar receber pagamentos apenas pela **API**.

Você deve ativar esta opção em "Integrações" > "[Pagamentos via API](https://pagseguro.uol.com.br/integracao/pagamentos-via-api.jhtml)".

Apenas com isso já é possível receber os pagamentos e fazer o retorno automático de dados.

= Configurações do Plugin: =

Com o plugin instalado acesse o admin do WordPress e entre em "WooCommerce" > "Configurações" > "Finalizar compra" > "PagSeguro".

Habilite o PagSeguro, adicione o seu e-mail e o token do PagSeguro. O token é utilizado para gerar os pagamentos e fazer o retorno de dados.

Você pode conseguir um token no PagSeguro em "Integrações" > "[Token de Segurança](https://pagseguro.uol.com.br/integracao/token-de-seguranca.jhtml)".

É possível escolher entre três opções de pagamento que são:

- **Checkout no PagSeguro (padrão):** O cliente e redirecionado para o site do PagSeguro
- **Checkout em Lighbox:** O cliente permance no seu site é aberto um Lightbox do PagSeguro onde o cliente fará o pagamento
- **Checkout Transparente:** O cliente faz o pagamento direto em seu site na página de finalizar pedido utilizando a API do PagSeguro.

Você ainda pode definir o comportamento da integração utilizando as opções:

- **Enviar apenas o total do pedido:** Permite enviar apenas o total do pedido no lugar da lista de itens, esta opção deve ser utilizada apenas quando o total do pedido no WooCommerce esta ficando diferente do total no PagSeguro.
- **Prefixo de pedido:** Esta opção é útil quando você esta utilizando a mesma conta do PagSeguro em várias lojas e com isso você pode diferenciar os pagamentos pelo prefixo.

= Checkout Transparente =

Para utilizar o checkout transparente é necessário utilizar o plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/).

Com o **WooCommerce Extra Checkout Fields for Brazil** instalado e ativado você deve ir até "WooCommerce > Campos do Checkout" e configurar a opção "Exibir Tipo de Pessoa" como "Pessoa Fisíca apenas".

Isto é necessário porque é obrigatório o envio de CPF para o PagSeguro, além de que o PagSeguro aceita apenas CPF.

Note que é necessário aprovação do PagSeguro para utilizar o Checkout Transparente, saiba mais em "[Como receber pagamentos pelo PagSeguro](https://pagseguro.uol.com.br/receba-pagamentos.jhtml)".

Pronto, sua loja já pode receber pagamentos pelo PagSeguro.

== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

= What is needed to use this plugin? =

* WooCommerce version 2.1 or latter installed and active.
* Only one account on [PagSeguro](http://pagseguro.uol.com.br/ "PagSeguro").

### FAQ em Português: ###

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin WooCommerce 2.1 ou superior.
* Possuir uma conta no PagSeguro.
* Gerar um token de segurança no PagSeguro.
* Ativar os pagamentos via API.
* Utilizar uma das duas opções de configuração no PagSeguro (veja elas no guia de [instalação do plugin](http://wordpress.org/plugins/woocommerce-pagseguro/installation/)).

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

Sim é possível, basta utilizar o plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/).

= O pedido foi pago e ficou com o status de "processando" e não como "concluído", isto esta certo ? =

Sim, esta certo e significa que o plugin esta trabalhando como deveria.

Todo gateway de pagamentos no WooCommerce deve mudar o status do pedido para "processando" no momento que é confirmado o pagamento e nunca deve ser alterado sozinho para "concluído", pois o pedido deve ir apenas para o status "concluído" após ele ter sido entregue.

Para produtos baixáveis a configuração padrão do WooCommerce é permitir o acesso apenas quando o pedido tem o status "concluído", entretanto nas configurações do WooCommerce na aba *Produtos* é possível ativar a opção **"Conceder acesso para download do produto após o pagamento"** e assim liberar o download quando o status do pedido esta como "processando".

= Ao tentar finalizar a compra aparece a mensagem "PagSeguro: Um erro ocorreu ao processar o seu pagamento, por favor, tente novamente ou entre em contato para obter ajuda." o que fazer? =

Esta mensagem geralmente aparece por causa que não foi configurado um **Token válido**.  
Gere um novo Token no PagSeguro em "Integrações" > "[Token de Segurança](https://pagseguro.uol.com.br/integracao/token-de-seguranca.jhtml)" e adicione ele nas configurações do plugin.

Outro erro comum é gerar um token e cadastrar nas configurações do plugin um e-mail que não é o proprietário do token, então tenha certeza que estes dados estão realmente corretos!

Note que caso você esteja utilizando a opção de **sandbox** é necessário usar um e-mail e token de teste que podem ser encontrados em "[PagSeguro Sandbox > Dados de Teste](https://sandbox.pagseguro.uol.com.br/comprador-de-testes.html)".

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

Sim, funciona e basta você ativar isso nas opções do plugin, além de configurar o seu token de desenvolvedor.

Para conseguir o token de desenvolver você deve acessar "[PagSeguro Sandbox > Dados de Teste](https://sandbox.pagseguro.uol.com.br/comprador-de-testes.html)".

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

= 2.10.3 - 2015/08/19 =

* Melhoradas as mensagens de erro durante o checkout.

= 2.10.2 - 2015/08/08 =

* Corrigido erro na tradução pt_BR do plugin.

= 2.10.1 - 2015/08/08 =

* Corrigido os links dos alertas sobre opções obrigatórios não configuradas do plugin.

= 2.10.0 - 2015/08/08 =

* Adicionado suporte para WooCommerce 2.4.x.
* Removido suporte para WooCommerce 2.0.x.

= 2.9.0 - 2015/06/23 =

* Adicionado método para ignorar a opção "Manter Estoque (minutos)" do WooCommerce.

= 2.8.1 - 2015/02/07 =

* Melhorado o suporte do Checkout Transparente no WooCommerce 2.3.

= 2.8.0 - 2015/02/03 =

* Adicionado suporte para o WooCommerce 2.3.
* Adicionado suporte para WooCommerce Multilingual. 
* Adicionado recurso para utilizar o endereço de entrega no Lightbox (antes funcionava apenas com checkout normal ou transparente).
* Adicionada alerta de erro para CPF invalido com checkout transparente.
* Correções na tradução do plugin.

= 2.7.4 - 2014/11/05 =

* Adicionado alerta quando o usuário não preenche o campo bairro no checkout transparente.

= 2.7.3 - 2014/10/26 =

* Adicionadas mensagens de erro para DDD e CEP inválidos.

= 2.7.2 - 2014/10/11 =

* Melhorado o salvamento dos detalhes do pedido que o PagSeguro retorna para a loja, como link de boleto, método de pagamento utilizando e outros.

= 2.7.1 - 2014/09/29 =

* Corrigido link dos dados de sandbox
* Adicionado suporte para _transaction_id do WooCommerce 2.2

= 2.7.0 - 2014/08/16 =

* Adicionado sistema de templates para personalizar os templates de checkout e outros. Para fazer isso basta copiar a pasta `templates/` deste plugin para dentro do seu tema, devendo ficar como `woocommerce/pagseguro/`.
* Corrigido o nome do arquivo principal do plugin.

= 2.6.2 - 2014/08/07 =

* Corrigido o script que escondes os botões "Pagar com PagSeguro" e "Cancelar pedido &amp; restaurar carrinho" quando o Lightbox é carregado.

= 2.6.1 - 2014/08/04 =

* Corrigido o valor individual de cada produto.

= 2.6.0 - 2014/08/02 =

* Melhoria na lista de itens do pedido que é enviado para o PagSeguro, agora é possível ver o total de taxas enviadas.
* Adicionada a opção "Enviar apenas o total do pedido" que envia apenas o total para o PagSeguro no lugar da lista de itens do pedido.
* Corrigida a exibição da opção "Prefixo de pedido" na página de opções do plugin.

= 2.5.1 - 2014/07/09 =

* Adicionada feature que permite usar o Checkout Transparente sem precisar digitar uma descrição para o método de pagamento.
* Correção do checkout com Lightbox.

= 2.5.0 - 2014/07/08 =

* Implementando o Checkout Transparente do PagSeguro.
* Melhorada todo o código de integração para tornar possível trabalhar bem o checkout padrão, Lightbox e Checkout Transparente.
* Melhoria nas mensagens de erro.

= 2.4.1 - 2014/06/12 =

* Corrigida a URL de notifição para versões 2.0.x do WooCommerce.

= 2.4.0 - 2014/06/10 =

* Correções nas mensagens do log para a criação de tokens de pagamento.
* Adicionada opção de ambiente sandbox.

= 2.3.1 - 2014/05/24 =

* Melhoria nos status do pedido, agora ao gerar um boleto o pedido é alterado para "aguardando".
* Modificado o botão "Finalizar pedido", com o WooCommerce 2.1 ou superior vai mostrar a mensagem "Realizar pagamento".

= 2.3.0 - 2014/04/02 =

* Adicionada opção para selecionar pagamento direto com redirecionamento ou pelo Lightbox do PagSeguro.

= 2.2.1 - 2013/12/06 =

* Melhoria na compatibilidade com o WooCommerce 2.1.

= 2.2.0 - 2013/12/04 =

* Corrigido padrões de código.
* Removida compatibilidade com versões 1.6.x ou inferiores do WooCommerce.
* Adicionada compatibilidade com WooCommerce 2.1 ou superior.

= 2.1.1 - 2013/09/03 =

* Adicionada mensagem sobre DDD errado nas mensagens de erro do PagSeguro.
* Correção da verificação do IPN.

= 2.1.0 - 2013/08/29 =

* Adicionada função para tratar as mensagens de erro do PagSeguro para CPF, CEP e número de telefone.

= 2.0.3 - 2013/08/22 =

* Correção da alteração de status pelo pela notificação de pagamento do PagSeguro.

= 2.0.2 - 2013/08/22 =

* Corrigido o erro causado com números de telefone sem DDD.

= 2.0.1 - 2013/08/19 =

* Removida a obrigatoriedade de enviar os campos de endereço.

= 2.0.0 - 2013/08/17 =

* Adicionadas as novas APIs de pagamentos e notificações do PagSeguro.
* Removidas as APIs antigas de pagamento e notificações do PagSeguro.
* Melhoria nos status de pagamento.
* Melhorias na notificações sobre compras em disputas ou que tiveram o pagamento devolvido.

= 1.6.1 - 2013/08/14 =

* Melhoria no JavaScript inline no formulário de checkout.

= 1.6.0 - 2013/07/26 =

* Adicionado o filtro `woocommerce_pagseguro_icon` para troca do ícone do método de pagamento.
* Melhoria no filtro `woocommerce_pagseguro_args`, agora ele aceita o objeto `WC_Order` no lugar do ID.
* Melhoria nas opções do plugin.
* Melhoria nas mensagens de status do pedido.

= 1.5.0 - 2013/06/24 =

* Adicionado link de `Configurações` na página de plugins.
* Melhorias no código.
* Adicionado suporte para WooCommerce 2.1.
* Adicionado o ID da compra no filtro `woocommerce_pagseguro_args`.

= 1.4.0 - 2013/04/02 =

* Correção do retorno automático de dados na versão 2.0.0 ou superior do WooCommerce.

= 1.3.4 - 2013/03/06 =

* Corrigida a compatibilidade com WooCommerce 2.0.0 ou mais recente.

= 1.3.3 - 2013/02/08 =

* Corrigido o hook responsavel por salvar as opções para a versão 2.0 RC do WooCommerce.

= 1.3.2 - 2013/02/08 =

* Plugin corrigido para a versão 2.0 do WooCommerce.

= 1.3.1 - 2012/12/08 =

* Melhoria no método que atualiza o status do pedido.
* Correção da quantidade de caracteres das descrição dos produtos no PagSeguro.

= 1.3.0 - 2012/11/30 =

* Adicionada opção para logs de erro.
* Adiciona opção para validar ou não endereço (quando ativo força cliente a informar os dados corretamente e ir direto para a página de pagamento do PagSeguro).

= 1.2.2 - 2012/11/19 =

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

= 2.10.3 =

* Melhoradas as mensagens de erro durante o checkout.

== License ==

WooCommerce PagSeguro is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

WooCommerce PagSeguro is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with WooCommerce PagSeguro. If not, see <http://www.gnu.org/licenses/>.
