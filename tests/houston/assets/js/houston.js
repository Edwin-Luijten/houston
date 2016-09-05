var Houston = {
    problemsResource: null,
    problems: [],
    storage: $.localStorage,
    template: $('#problem-template > .panel'),
    stackOverflowUrl: 'http://stackoverflow.com/search?q=php+',

    init: function () {
        if (this.problemsResource === null) {
            console.error('Please set a location to get problems from.');
            return;
        }

        this.getProblems();
        this.enableFilterByLevel();
    },

    getProblems: function () {
        //if(this.storage.isEmpty('problems')) {
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
                    //self.storage.set('problems', self.problems);
                    self.showProblems();
                    $('.problem-counter').countTo({
                        from: 0,
                        to: self.problems.length,
                        speed: 800,
                        refreshInterval: 100
                    });

                }
            })
        });
        // } else {
        //     this.problems = this.storage.get('problems');
        //     this.showProblems();
        // }
    },

    showProblems: function () {
        var self = this;

        // Reverse to get newest at the top
        $(this.problems.reverse()).each(function (key, value) {
            var title = value.data.body.abstract_payload.exception.class;
            var level = value.data.level;
            var message = value.data.body.abstract_payload.exception.message;
            var line = value.data.body.abstract_payload.exception.line;
            var context = self.getContext(value);
            var timestamp = moment.unix(value.data.timestamp);
            var template = self.template.clone();

            // Fill template

            template.addClass('panel-' + self.levelToCss(level));
            template.attr('data-filter-level', level);
            template.find('.panel-heading').text(timestamp.format('DD-MM-YYYY HH:mm:ss') + ' - ' + title);
            template.find('p:first').html(message + ' on line ' + line + ' <a href="#collapse-' + key + '" data-toggle="collapse" class="pull-right">toggle context</a><br/>');
            template.find('.pane-traceback').html(context);

            template.find('.collapse').attr('id', 'collapse-' + key);
            template.find('.nav-tabs li.tab-traceback').find('a').attr('href', '#traceback-' + key);
            template.find('.nav-tabs li.tab-browser-os').find('a').attr('href', '#browser-os-' + key);
            template.find('.nav-tabs li.tab-stack-overflow').find('a').attr('href', self.stackOverflowUrl + title.replace(' ', '+'));

            template.find('.pane-traceback').attr('id', 'traceback-' + key);
            template.find('.pane-browser-os').attr('id', 'browser-os-' + key);

            // Append template
            $('#problems').append(template);
        });

        // code highlighting
        hljs.initHighlightingOnLoad();
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
        console.log(level);
        if (level == 'debug') {
            return 'primary';
        } else if (level == 'error') {
            return 'danger';
        } else if (level == 'warning') {
            return 'warning';
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