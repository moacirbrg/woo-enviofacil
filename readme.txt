=== WooCommerce Envio Fácil ===
Contributors: moacirbrg
Tags: shipping, delivery, woocommerce, enviofacil, envio fácil
Requires at least: 4.6
Tested up to: 4.9
Stable tag: 0.1.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds Envio Fácil shipping methods to WooCommerce

== Description ==

Utilize os métodos de entrega do Envio Fácil, um serviço disponível para lojas que utilizam PagSeguro como meio de pagamento.

[Envio Fácil](https://pagseguro.uol.com.br/para-seu-negocio/online/envio-facil) é um método de entrega brasileiro. Ele utiliza os serviços PAC e SEDEX dos Correios, porém com a vantagem de contrato entre o Correios e a empresa reponsável pelo Envio Fácil, deixando muito menor o custo de envio para lojas de micro e pequeno porte.

O desenvolvimento deste plugin tem apoio da loja [Clubinho Nerd](https://clubinhonerd.com.br/), ele não tem incentivo algum do PagSeguro/UOL. O que motiva a criação de plugin é a redução dos custos de frete para o consumidor em até 30% mantendo o mesmo nível de qualidade já conhecido pelo Correios e resultando numa maior competitividade dos pequenos lojistas.

= Funcionalidades =

Este plugin oferece os seguintes recursos:

- Entrega nacional
 - PAC
 - SEDEX
- Possibilidade de aplicar desconto ou taxa ao serviço de entrega
- Possibilidade de adicionar prazo extra para a entrega

= Instalação: =

Veja mais sobre a instalação e configuração na aba [Installation](http://wordpress.org/plugins/woocommerce-envio-facil/installation/).

= Compatibilidade =

Requer WooCommerce 3.0 ou posterior para funcionar

= Serviços de terceiros =

Este plugin não coleta seus dados, contudo ele se comunica com o web service do Envio Fácil para obter os preços e prazos do envio. Não garantimos o uso dos dados por parte do web service, mas anexamos abaixo os dois termos que encontramos relacionados ao web service:
- [Regras de uso](https://pagseguro.uol.com.br/sobre/regras-de-uso)
- [Normas de segurança e privacidade](https://sac.uol.com.br/info/protecao_privacidade/normas_protecao_privacidade.jhtm)

Se você desejar, você também pode acessar o [site do serviço](https://pagseguro.uol.com.br/para-seu-negocio/online/envio-facil).

Para solicitarmos os preços e prazos para o web service, enviamos os seguintes dados sob HTTPS (comunicação criptografada):
- CEP de origem
- CEP de destino
- Largura do pacote
- Altura do pacote
- Comprimento do pacote
- Peso aproximado do pacote

Além disso, como é uma comunicação em rede, o web service deles é capaz de obter o IP do servidor que está fazendo a consulta.

Dados sensíveis que possam identificar o cliente da loja e o que ele estaria comprando NÃO SÃO ENVIADOS por este plugin.

== Installation ==

= Instalação do plugin =

- Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
- Ative o plugin.

= Requisitos =

- [JSON](https://secure.php.net/manual/pt_BR/book.json.php) ativado no PHP (geralmente está presente na instalação padrão do PHP).
- [cURL](http://php.net/manual/pt_BR/book.curl.php) ativado no PHP (geralmente já está ativado em planos de hospedagem de sites).

= Configurações do plugin =

[youtube https://youtu.be/teMrY0ZNFMQ]

= Configurações dos produtos =

É necessário configurar o peso e as dimensões dos produtos que estarão disponíveis para entrega com este método, do contário eles não serão cotados no web service do Envio Fácil.