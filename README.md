# Simple crawl a website with spatie/crawler

Some demos about using spatie/crawler to crawl a website

## Run demo

### Prepare

- Clone project
- Run `composer install`
- Try to run a demo :)

### Explain 5 demos

1. demo1 : I think this is a very simple crawler :D
  
- run : From project root folder `php demo1/run.php`
- output : You should see html code of Google home page :) 

2. demo2 : A simple demo with spatie/crawler

- run : From project root folder `php demo2/run.php`
- output : Something like below

```
"==> Crawled Crawled https://www.youtube.com/"
"==> Crawled Crawled https://www.youtube.com/upload"
"==> Crawled Crawled https://www.youtube.com/feed/guide_builder"
"==> Crawled Crawled https://www.youtube.com/feed/history"
"==> Crawled Crawled https://www.youtube.com/channel/UCYfdidRxbB8Qhf0Nx7ioOYw"
"==> Crawled Crawled https://support.google.com/youtube/answer/1738660?hl=vi"
"==> Crawled Crawled https://www.youtube.com/feed/trending?disable_polymer=1"
```
3. demo3 : A simple demo with spatie/crawler, but in **Observer** i will try to get page title

- run : From project root folder `php demo3/run.php`
- output : Something like below

```
==> Crawled https://www.youtube.com/
 |_ Title : YouTube
==> Crawled https://www.youtube.com/feed/history
 |_ Title : Lịch sử
 - YouTube
==> Crawled https://www.youtube.com/channel/UCYfdidRxbB8Qhf0Nx7ioOYw
 |_ Title : Tin tức
 - YouTube
==> Crawled https://www.youtube.com/feed/trending
 |_ Title : Thịnh hành
 - YouTube
==> Crawled https://www.youtube.com/feed/trending?disable_polymer=1
 |_ Title : Thịnh hành
 - YouTube
==> Crawled https://www.youtube.com/channel/UCzuqhhs6NWbgTzMuM09WKDQ
 |_ Title : Thực tế ảo
 - YouTube
```

4. demo4 : I customized some class to get more feature. 
I can ask crawler to run follow my path, instead of crawling all links on the page.

- Define the path: in `demo4/src/LinkAdder.php`

```php
protected $rules = [
    'root' => [ // from root
        'new_documents' => [ // how to go to new documents list page
            // follow the link with css selector
            'selector' => 'nav.navbar-static-top ul.navbar-left li:nth-child(2) a', 
            ]
        ],
    'new_documents' => [ // from new documents list page
            'document_detail' => [ // go to document detail pages
                'selectors' => 'ul.media-list .media-heading a', // follow all links with css selector
            ],
            'new_documents' => [ // go to next new documents list page 
                'selector' => 'ul.pagination a[rel=next]', // follow the link with css selector
            ]
        ]
    ];
```
- At here, `root` mean the url is passed when you initialize `Crawler`, 
  in this demo is [https://www.epubbooks.com/](https://www.epubbooks.com/)

- run : From project root folder `php demo4/run.php`
- output : Something like below

```
"==> [root] Crawled https://www.epubbooks.com/"
"\tepubBooks - Download Free Kindle ePub eBooks"
"==> [new_documents] Crawled https://www.epubbooks.com/books"
"\tBook Listing (Page #1)"
"==> [document_detail] Crawled https://www.epubbooks.com/book/2157-wizard-of-venus"
"\tThe Wizard of Venus by Edgar Rice Burroughs"
"==> [document_detail] Crawled https://www.epubbooks.com/book/2146-poo-poo-and-the-dragons"
"\tPoo-Poo and the Dragons by C. S. Forester"
"==> [document_detail] Crawled https://www.epubbooks.com/book/2153-man-from-bar-20"
"\tThe Man From Bar 20 by Clarence E. Mulford"
"==> [document_detail] Crawled https://www.epubbooks.com/book/2155-eyeless-in-gaza"
"\tEyeless in Gaza by Aldous Huxley"
"==> [document_detail] Crawled https://www.epubbooks.com/book/2151-last-egyptian"
"\tThe Last Egyptian by L. Frank Baum"
```

5. demo5 : same as demo3 but i customized a `CrawlQueue` with a db system. At here is `sqlite`.

- Before run you should run `touch demo5/queue.db` to init demo db.
- run : From project root folder `php demo4/run.php` and `Ctrl +c` before crawler stop, and then run `php demo4/run.php` 
again. The crawler will continue from INIT links instead of rerun from `root` 

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
