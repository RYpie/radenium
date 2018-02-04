![GitHub license](https://img.shields.io/badge/license-GPL-blue.svg)


# Welcome To Radenium, Home Of The Great Radeniums



Idea of Radenium is to implement an HLS livestreaming application based on FFMPEG, wrapped by a python application.

Development status is definitely prototype, works only on my mac...

Without knowledge on python and mysql I would not put too much effort in this for now.


## Installation

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
- Modify mod_settings.py in the addons/mod_setting directory
- Download radenium and follow instruction in chapter Python client.


## Python client

Check it out, either run:

- $ python3 radenium.py

- Press 'g' and ENTER should start a livestream of your camera. Files stored in client/python/media (same directory where the addons directory resides).

- Press 's' and ENTER should stop the livestream.

- Or run the encoderdeck_app or use the muxer in mod_mediadevices.

## Radenium Loves

- https://www.joomla.org
- https://www.python.org
- https://www.kodi.tv
- https://www.ffmpeg.org
- https://www.shotcut.org
- https://www.mltframework.org
- https://www.apple.com
- HLS livestreaming
- Podcasting
- RSS

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
