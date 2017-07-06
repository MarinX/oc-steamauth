# Steam Auth
A simple steam API authetication for [OctoberCMS](https://octobercms.com) by Marin.

# Installation and Setup

1. Go to system on the main menus in your backend.
2. Click on the settings subsection.
3. Under the **Steam** area, there should be a plugin setting called "Steam API", click on that.
4. Insert your steam API key, which can be obtain here [http://steamcommunity.com/dev/apikey](http://steamcommunity.com/dev/apikey)
5. Insert relative path for page after authorization (eg /steam or /login) - this is where you will get your session user info
6. Enable HTTPS if you are using https

# Usage
The steam login route is **/marin/steamauth/login** eg. http://yourwebsite.com/marin/steamauth/login

- You will be then redirected to steam page where you need to login to your account.
- After login, you will be redirected to the page you specifed on settings **redirect**.
- You can now grab session **steamuser** for user info or **steamerror** for any error.

Page code section
```php
public function onEnd() 
{
    $this['steamuser'] = Session::get('steamuser');
    $this['steamerror'] = Session::get('steamerror');
}
```
Page markup section
```php
{% if steamuser %}
SteamID: {{steamuser['steamid']}}
Profile: {{steamuser['profileurl']}}
...etc
{% endif %}
```

Full reference of object can be found on [steamAPI](https://developer.valvesoftware.com/wiki/Steam_Web_API#GetPlayerSummaries_.28v0002.29)


# Demo
[https://marin-basic.com/steamauth](https://marin-basic.com/steamauth)

**no sessions or data is stored, so please dont abuse the login!**

# Donate
Got some crypto ?

ETH: 0x9b5437b13d7bd585bfd6212fab7aa8ac109ee81e

BTC: 3Ld3oCASP9zWKy1gc7EqEyC4uozwZdo8cV
