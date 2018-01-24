![GitHub license](https://img.shields.io/badge/license-GPL-blue.svg)

# Welcome To Radenium, Home Of The Great

Idea of Radenium is to implement an HLS livestreaming application based on FFMPEG, wrapped by a python application, controlled via a Joomla! component. The python application interacts with the Joomla! component via the database. Python application can also be used standalone.

Development status is prototype

Idea is to run the application on a barebone PC and via a webadmin control the livestreaming that is being pushed to a website.

Currently it works on a mac, my mac to be clear. Without knowledge on python, Joomla! and mysql I would not put too much effort in this for now.

Aim is to add installation instructions and more complete installation packages for users to hook in.

## Installation

Take notice that radenium should work without database and Joomla! The database and Joomla! is only used to create a webui and manage the files etcetera. By the way, at this point it's really nothing worth putting  efforts in.

Aim is to create a simple, nodatabase required, solution to publish a stream to the web.

### MAC OSX
If you don't have python3 installed:
- $ brew install python3

If you don't have ffmpeg installed:
- $ brew install ffmpeg   # if you don't have ffmpeg already

Download mysql-connector-python-2.0.4 and cd yourself into the package directory, then:
- $ python3 setup.py install

### Linux
@todo

### All platforms
- Download and install a webserver, preferably XAMP, MAMP or a NGINX solution with MYSQL
- Download and install Joomla! (http://www.joomla.org)
- Modify mod_settings.py in the addons/mod_setting directory
- Download radenium and follow instruction in chapter Python client.


## Python client

Check it out, either run:

- $ python3 radenium.py

- Press 'g' and ENTER should start a livestream of your camera. Files stored in client/python/media (same directory where the addons directory resides).

- Press 's' and ENTER should stop the livestream.

- Or run the encoderdeck_app or use the muxer in mod_mediadevices.

## PHP Joomla! server
Joomla package is intended to be used on the same system as where the python is running.
If you are able to install it, add a menu, the default menu of radenium encoder task. 
Next add a task of which you can find an example in the python addons/app_eventhandler




Roadmap:
- Use a real ffmpeg wrapper.
- Things that are top secret for now, really.


# Copyright

Copyright 2017 Andries Bron
This file is part of Radenium.

    Radenium is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Radenium is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Radenium.  If not, see <http://www.gnu.org/licenses/>.
