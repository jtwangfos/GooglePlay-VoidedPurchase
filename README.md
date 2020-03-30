# GooglePlay-VoidedPurchase
> A handler for GooglePlay with voided purchase.


+ First, you need to install google api sdk with composer, [see](https://github.com/googleapis/google-api-php-client).

+ then:
<pre>
  require_once '/path/to/Handler.php';
  
  $handler = new Handler('some_package_name', 'some_googleplay_json_secret', 'some_json_secret_path');
  $handler->process($params, somecallback()); // return false if voided purchase is empty.
</pre>
