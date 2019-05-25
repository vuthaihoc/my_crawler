<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 5/24/19
 * Time: 13:52
 */

namespace MyCrawler\Demo5;


use Spatie\Crawler\CrawlQueue\CrawlQueue;
use Illuminate\Database\Capsule\Manager as Capsule;
use Spatie\Crawler\CrawlUrl;
use Spatie\Crawler\Exception\UrlNotFoundByIndex;

class CrawlSqliteQueue implements CrawlQueue {
	
	protected $capsule;
	
	const STATUS_INIT = 0;
	const STATUS_VISITING = 10;
	const STATUS_VISITED = 100;
	
	/**
	 * CrawlSqliteQueue constructor.
	 */
	public function __construct() {
		$this->capsule = new Capsule;
		
		$this->capsule->addConnection([
			'driver'    => 'sqlite',
			'host'      => 'localhost',
			'database'  => __DIR__ . "/../queue.db",
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		]);
		
		$this->capsule->setAsGlobal();
//		Capsule::schema()->dropIfExists( 'stack');
		if(!Capsule::schema()->hasTable( 'stack')){
			Capsule::schema()->create('stack', function ($table) {
				$table->increments('id');
				$table->string('step');
				$table->string('url')->unique();
				$table->integer('status');
			});
		}
//		dd(Capsule::table( 'stack')->where('status', self::STATUS_INIT)->take(2)->get(),
//			Capsule::table( 'stack')->where('status', self::STATUS_VISITED)->take(2)->get());
		
	}
	
	public function add( CrawlUrl $url ): CrawlQueue {
		if($this->has( $url )){
			return $this;
		}
		
		$inserted = Capsule::table( 'stack')->insertGetId( [
			'step' => $url->url->getStep(),
			'url' => $url->url,
			'status' => self::STATUS_INIT,
		]);
		
		if($inserted){
			$url->setId( $inserted );
		}
		
		return $this;
	}
	
	public function has( $crawlUrl ): bool {
		
		if (! $crawlUrl instanceof CrawlUrl) {
			$crawlUrl = CrawlUrl::create($crawlUrl);
		}
		
		return Capsule::table( 'stack' )
		              ->where( 'step', $crawlUrl->url->getStep() )
		              ->where( 'url', $crawlUrl->url )
		              ->exists();
	}
	
	public function hasPendingUrls(): bool {
		
		return Capsule::table( 'stack' )
		              ->where( 'status', self::STATUS_INIT )
		              ->exists();
		
	}
	
	public function getUrlById( $id ): CrawlUrl {
		$first = Capsule::table( 'stack' )
		              ->where( 'id', $id )
		              ->first();
		if($first){
			$crawlUrl = CrawlUrl::create( new MyUri($first->url, $first->step));
			$crawlUrl->setId( $first->id );
			return $crawlUrl;
		}else{
			throw new UrlNotFoundByIndex("#{$id} crawl url not found in collection");
		}
	}
	
	/** @return \Spatie\Crawler\CrawlUrl|null */
	public function getFirstPendingUrl() {
		$first = Capsule::table( 'stack' )
			              ->where( 'status', self::STATUS_INIT )
			              ->first();
		if($first){
			$crawlUrl = CrawlUrl::create( new MyUri($first->url, $first->step));
			$crawlUrl->setId( $first->id );
			return $crawlUrl;
		}else{
			return null;
		}
	}
	
	public function hasAlreadyBeenProcessed( CrawlUrl $url ): bool {
		return Capsule::table( 'stack' )
		              ->where( 'id', $url->getId() )
		              ->where( 'status', self::STATUS_VISITED )
		              ->exists();
	}
	
	public function markAsProcessed( CrawlUrl $crawlUrl ) {
		Capsule::table( 'stack' )
		       ->where( 'id', $crawlUrl->getId() )
		       ->update(['status' => self::STATUS_VISITED]);
	}
}