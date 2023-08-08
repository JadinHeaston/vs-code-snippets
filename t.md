# Markdown Support Testing

This is a test markdown document showcasing support of several features, like nested headings.

## Lists

### Ordered

Ordered lists can be created by:

1. Starting a new line.
2. Placing the desired position as an integer.
3. Follow the integer by a period (`.`) and a space.
2. Does this editor support lists?

### Unordered

* An unordered list can be created by using an asterisk followed by a space on a new line.
* They indicate that the list has no particular order.
* Does this editor support unordered lists?

## Code Blocks

### Single-Line

A single line can be done by using a single back-tick before and after the text. `They indicate that the text inside the block are to be rendered exactly as typed.`

### Multi-Line

They can also be used to represent multiple lines by using triple back-ticks, optionally followed by the programming language.  
This provides additional context for syntax highlighting.

Code snippet

```php
function adfsLoginResponseProcessing(string $context)
{
	$adfs = new AdfsBridge();
	try
	{
		$userDetails = $adfs->getAdfsSignInResponse(
			AdfsConf::getInstance(),
			$_POST['wa'],
			$_POST['wresult'],
			$_POST['wctx']
		);

		//Set the user details in session.
		$_SESSION['AdfsUserDetails'] = $userDetails;
		if (additionalAuthentication() === false) //Allowing user processing of data.
			session_unset();
	}
	catch (Exception $e)
	{
		printf('Message: ' . $e->getMessage());
	}
}
```
