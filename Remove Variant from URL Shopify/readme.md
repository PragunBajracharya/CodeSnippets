# Remove variant ID from the URL

Search for window.location.protocol in the main js file of the theme
<br>
`window.location.protocol+"//"+window.location.host+window.location.pathname+"?variant="+this.currentVariant.id`

The above code or something similar code willbe found in the main theme js file. <br>
Remove the `+"?variant="+this.currentVariant.id` part.