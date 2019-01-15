import Routing from '../routing.js';

$(document).ready(function () {
    $('.js-like-btn').on('click', function (e) {
        e.preventDefault();
        if ($(this).hasClass('not-allowed')) return false;

        let $this = $(this),
            $btnGroup = $this.closest('.js-like'),
            $like = $btnGroup.find('[data-type="1"] span'),
            $dislike = $btnGroup.find('[data-type="-1"] span')
        ;

        app.request($this.attr('href')).then(function(data) {
            if (data.type === 'success') {
                $like.html(data.data.likes);
                $dislike.html(data.data.dislikes);
            }
        });
    });

    $('.js-bookmark-article-btn').on('click', function (e) {
        e.preventDefault();
        if ($(this).hasClass('not-allowed')) return false;

        let $this = $(this),
            $bookmarkValue = $this.find('span')
        ;

        app.request($this.attr('href')).then(function(data) {
            if (data.type === 'success') {
                $bookmarkValue.html(data.data);
            }
        });
    });

    $('.js-comment-reply-btn').on('click', function (e) {
        e.preventDefault();
        if ($(this).hasClass('not-allowed')) return false;

        let $this = $(this),
            collapse = $this.closest('.collapse'),
            textarea = $this.closest('.js-comment-reply').find('textarea'),
            text = textarea.val(),
            url = Routing.generate('api_comment_reply', { id: $this.data('id') })
        ;

        app.request(url, { text: text }, 'post').then(function(data) {
            if (data.type === 'success') {
                collapse.removeClass('show');
                textarea.val('');
            }
        });
    });
});