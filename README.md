# Introduction

Working on something incredible huge here. Unfortunately I cannot tell you what, but it is opensource or so.
Radenium is made to work on a mac, linux follows soon after.

Radenium comprises a media devices multiplexer app built around ffmpeg. Well, built is quite a statement, I mean I am busy building it...


# Radenium

Radenium is currently a prototype of a prototype, just to be clear.
Idea of Radenium is to control a video publishing platform using python and a Joomla! component.
Currently it works on a mac, my mac to be clear. So due to its state, I assume you to have knowledge on python, Joomla! and mysql. Otherwise I would not put too much effort in this for now, check out in a while again, for I aim to add installation instructions and more complete installation packages for you to hook in.

## Installation

Take notice that radenium should work without database and Joomla! The database and Joomla! is only used to create a webui and manage the files etcetera.

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
- Download and install a AMP package, preferably XAMP or MAMP
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
