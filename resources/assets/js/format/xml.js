String.prototype.removeLineEnd = function () {
        return this.replace(/(<.+?\s+?)(?:\n\s*?(.+?=".*?"))/g, '$1 $2')
    }
    function formatXml(text) {
        text = '\n' + text.replace(/(<\w+)(\s.*?>)/g, function ($0, name, props) {
                    return name + ' ' + props.replace(/\s+(\w+=)/g, " $1");
                }).replace(/>\s*?</g, ">\n<");

        text = text.replace(/\n/g, '\r').replace(/<!--(.+?)-->/g, function ($0, text) {
            var ret = '<!--' + escape(text) + '-->';
            return ret;
        }).replace(/\r/g, '\n');

        var rgx = /\n(<(([^\?]).+?)(?:\s|\s*?>|\s*?(\/)>)(?:.*?(?:(?:(\/)>)|(?:<(\/)\2>)))?)/mg;
        var nodeStack = [];
        var output = text.replace(rgx, function ($0, all, name, isBegin, isCloseFull1, isCloseFull2, isFull1, isFull2) {
            var isClosed = (isCloseFull1 == '/') || (isCloseFull2 == '/' ) || (isFull1 == '/') || (isFull2 == '/');
            var prefix = '';
            if (isBegin == '!') {
                prefix = getPrefix(nodeStack.length);
            }
            else {
                if (isBegin != '/') {
                    prefix = getPrefix(nodeStack.length);
                    if (!isClosed) {
                        nodeStack.push(name);
                    }
                }
                else {
                    nodeStack.pop();
                    prefix = getPrefix(nodeStack.length);
                }

            }
            var ret = '\n' + prefix + all;
            return ret;
        });

        var prefixSpace = -1;
        var outputText = output.substring(1);

        outputText = outputText.replace(/\n/g, '\r').replace(/(\s*)<!--(.+?)-->/g, function ($0, prefix, text) {
            if (prefix.charAt(0) == '\r')
                prefix = prefix.substring(1);
            text = unescape(text).replace(/\r/g, '\n');
            var ret = '\n' + prefix + '<!--' + text.replace(/^\s*/mg, prefix) + '-->';
            return ret;
        });

        return outputText.replace(/\s+$/g, '').replace(/\r/g, '\r\n');
    }
    function getPrefix(prefixIndex) {
        var span = '    ';
        var output = [];
        for (var i = 0; i < prefixIndex; ++i) {
            output.push(span);
        }

        return output.join('');
    }