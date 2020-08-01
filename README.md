ClearCobalt theme for [Question2Answer][1], aiming for a StackOverflow-ish look.

The style definitions are written in [Less][2], which supports variables and
macros (called "mixins"). To modify the style edit `qa-styles.less` and run the
Less compiler:

    lessc qa-styles.less > qa-styles.css

To validate the Less file run

    lessc --lint --verbose qa-styles.less

[1]: https://www.question2answer.org/
[2]: http://lesscss.org/
