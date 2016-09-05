var Houston = {
    problemsResource: null,
    problems: [],

    init: function () {
        if (this.problemsResource === null) {
            console.error('Please set a location to get problems from.');
            return;
        }

        this.getProblems();
        this.enableFilterByLevel();
    },

    getProblems: function () {
        var problemsFiles = $(this.problemsResource).data('problems');
        var total = problemsFiles.length;
        var i = 0;
        var self = this;

        $(problemsFiles).each(function (key, value) {
            $.ajax({
                url: value
            }).done(function (data) {
                i++;
                var lines = data.split('\n');

                $(lines).each(function (key, value) {
                    if (value.length > 0) {
                        self.problems.push(JSON.parse(value));
                    }
                });

                if (i === total) {
                    self.showProblems();
                }
            })
        });
    },

    showProblems: function () {
        var self = this;

        $(this.problems).each(function (key, value) {
            var title = value.data.body.abstract_payload.exception.class;
            var level = value.data.level;
            var message = value.data.body.abstract_payload.exception.message;
            var line = value.data.body.abstract_payload.exception.line;
            var context = self.getContext(value);

            $('#problems').append(
                '<div class="panel panel-' + self.levelToCss(level) + '" data-filter-level="' + level + '">' +
                '<div class="panel-heading">' + title + '</div>' +
                '<div class="panel-body">' + message + ' on line ' + line + ' <a href="#context-' + key + '" data-toggle="collapse" class="pull-right">toggle context</a><br/>' +
                '<div id="context-' + key + '" class="collapse">' + context + '</div></div>' +
                '</div>');


            hljs.initHighlightingOnLoad();
        });
    },

    getContext: function (problem) {
        var context = '';
        var frames = problem.data.body.abstract_payload.frames;

        $(frames).each(function (key, value) {
            context = context + '<pre><code class="php">';
            context = context + '// File: ' + value.filename + '<br/>';
            context = context + '// Line: ' + value.line_number + '<br/>';

            $(value.context).each(function (key, cnt) {

                $(cnt.pre).each(function (key, code) {
                    context = context + code + '\n';
                });
                context = context + '<span class="problem">';
                context = context + value.code;
                context = context + '</span>'; // + '\n';
                $(cnt.post).each(function (key, code) {
                    context = context + code + '\n';
                });
            });
            context = context + '</code></pre>';
        });

        return context.replace('<?php', this.escape('<?php'));
    },

    enableFilterByLevel: function () {
        var self = this;
        $('.btn-filter-level').click(function (event) {
            var value = $(this).data('value');
            $('#problems .panel').show();

            if (value !== 'all') {
                self.filterByLevel(value);
            }
        });
    },

    filterByLevel: function (level) {
        $('#problems .panel').not('[data-filter-level=' + level + ']').hide();
    },

    levelToCss: function (level) {
        if (level == 'debug') {
            return 'warning';
        } else if (level == 'error') {
            return 'danger';
        }

        return 'primary';
    },

    escape: function (string) {
        return string
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
}