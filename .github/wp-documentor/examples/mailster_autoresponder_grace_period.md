Adjust to one month

```php
add_filter( 'mailster_autoresponder_grace_period', function( $grace_period_in_seconds, $campaign_id ){
	return MONTH_IN_SECONDS;
},10 , 2 );
```

Disable the grace period

```php
add_filter( 'mailster_autoresponder_grace_period', '__return_false' );
```
