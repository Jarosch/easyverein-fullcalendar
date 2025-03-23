# easyverein-fullcalendar
This is a fork of the Wordpress Easyverein Plugin which integrates the Fullcalendar.io JS for calendar events. This fork was created on the last commitment by the author updating it to version 2.1.4. 

This fork improves the visibiity of events for the upcoming three months. That's how the Fullcalendar object is configured. Furthermore, Tippy.js is added as well and configured to show the event description while hovering the event in the calendar object.

![image](https://github.com/user-attachments/assets/3bad7049-4f3c-4bbd-9a5a-0bfa2adfd46c)

For the moment, this implementation is made for the public calendars, as there was no necessity for myself to make it also in private calendars available.
If somebody may feel the necessity to implement it for private calendars, feel free to reach out to me or to do it by yourself. 

I'm not the author of these files. Nevertheless, if the author is going to update his Wordpress plugin, I will keep on integrating my additions to the authors code as well, as long the author is not willing to add this kind of functionality on his end. 

# Installation
Just copy following file [public/shortcodes/easyVereinShortcodeCalendar.php](https://github.com/Jarosch/easyverein-fullcalendar/blob/340e33e74b04f6761ea1d0f262895140c1a962ed/public/shortcodes/easyVereinShortcodeCalendar.php) from this repository and overwrite it in your Wordpress installation in the plugin folder. Usually, the plugin folder is located at. 
```
/wp-content/plugins/easyverein
```
The file itself is allocated at following path: 
```
/wp-content/plugins/easyverein/public/shortcodes/
```
I hope, you will like it. 
