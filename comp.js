{
	"name": "spronkware/phphonex",
	"description": "PHP implementation of the Phonex name-matching algorithm",
	"keywords": ["soundex","metaphone","nyiss","name","matching"],
	"homepage": "https://github.com/spronkey/phphonex",
	"license": "MIT",
	"authors": [
		{
			"name": "Keith Humm",
			"email": "keith@spronkey.com",
			"homepage": "https://github.com/spronkey",
			"role": "Developer"
		}
	],
	"version": "0.0.1",
	"require": {
		"php": ">=5.3.0",
		"ext-mbstring": "*"
	},
	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/spronkey/phphonex"
		}
	],
	"autoload": {
		"psr-0": {
			"Spronkware\\PHPhonex": "src"
		}
	}
}