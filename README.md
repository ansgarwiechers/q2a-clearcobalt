Description
===========
ClearCobalt theme for [Question2Answer][1], aiming for a StackOverflow-ish look.

The style definitions are written in [Less][2], which supports variables and
macros (called "mixins"). To modify the style edit `qa-styles.less` and run the
Less compiler:

    lessc qa-styles.less > qa-styles.css

To validate the Less file run

    lessc --lint --verbose qa-styles.less

License
=======
This theme is licensed under the GNU General Public License version 3.0, except
for the included [Open Sans][3] and [Roboto Mono][4] fonts, which are licensed
under the terms of the Apache License version 2.0.

`SPDX-License-Identifier: GPL-3.0-or-later AND Apache-2.0`

[1]: https://www.question2answer.org/
[2]: http://lesscss.org/
[3]: https://fonts.google.com/specimen/Open+Sans
[4]: https://fonts.google.com/specimen/Roboto+Mono
