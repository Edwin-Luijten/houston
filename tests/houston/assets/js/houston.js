var Houston = {
    problemsResource: $('#problems-source'),
    problems: [],
    storage: $.localStorage,
    template: $('#problem-template > .panel'),
    stackOverflowUrl: 'http://stackoverflow.com/search?q=php+',
    filter: {
        history: 'today',
        level: 'all',
        all: 1,
        critical: 2,
        error: 3,
        warning: 4,
        info: 5,
        debug: 6,
        today: 10,
        yesterday: 11,
        thisWeek: 12,
        older: 13
    },

    init: function () {
        if (this.problemsResource === null) {
            console.error('Please set a location to get problems from.');
            return;
        }

        this.getProblems();
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

                   $('.problems-container').filterizr({
                       layout: 'vertical'
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
            var title = value.data.body.trace.exception.class;
            var level = value.data.level;
            var message = value.data.body.trace.exception.message;
            var line = value.data.body.trace.exception.line;
            var context = self.getContext(value);
            var timestamp = moment.unix(value.data.timestamp);
            var template = self.template.clone();
            var current = moment();

            // Fill template
            var diff = current.diff(timestamp, 'days');
            var history = '';
            var currentWeek = current.format('W');
            var week = timestamp.format('W');

            if (diff == 0) {
                history = history + self.filter['today'] +', ';
            } else if (diff == 1) {
                history = history + self.filter['yesterday'] +', ';
            }

            if (currentWeek == week) {
                history = history + self.filter['thisWeek'] + ', ';
            } else {
                history = history + self.filter['older'] + ', ';
            }

            template.addClass('panel-' + self.levelToCss(level));
            template.attr('data-category', (self.filter.all + ', ' + self.filter[level] + ', ' + history).slice(0, -2));

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
        var frames = problem.data.body.trace.frames;

        $(frames).each(function (key, value) {

            context = context + '<div class="source-info"><span class="source-file">File: ' + value.filename + '</span><br/>';
            context = context + '<span class="source-line">Line: ' + value.line_number + '</span></div>';
            context = context + '<pre><code class="php">';
            $(value.context).each(function (key, cnt) {

                $(cnt.pre).each(function (key, code) {
                    context = context + code;
                });
                context = context + '<span class="problem">';
                context = context + value.code;
                context = context + '</span>'; // + '\n';
                $(cnt.post).each(function (key, code) {
                    context = context + code;
                });
            });
            context = context + '</code></pre>';
        });

        return context.replace('<?php', this.escape('<?php'));
    },

    levelToCss: function (level) {

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