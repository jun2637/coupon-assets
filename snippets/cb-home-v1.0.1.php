/**
 * 쿠폰보관함 — 홈·사이드바 v1.0.1 (홈 좌우 여백 · 모바일 사이드바 목차 숨김)
 *
 * ⚠ WPCode → 새 스니펫 → 코드 유형 "PHP 스니펫" → 이 파일 전체 붙여넣기 (맨 위에 <?php 넣지 않음)
 *   실행 위치: "Run Everywhere(모든 곳)". 코드 데이터 시스템(nol-codes-system.php) v1.3이 함께 켜져 있어야 합니다.
 *
 * 숏코드
 *   [cb_last_verified]          가장 최근 검증일 (예: 2026.10.23)
 *   [cb_brands]                 브랜드 카드 (대표 혜택 · 코드 수 · 검증일 자동)
 *   [cb_closing days="7" limit="4" style="list|compact"]   만료가 가까운 혜택 (지나면 자동으로 빠짐)
 *   [cb_log limit="3"]          각 글의 '업데이트 사항' 첫 줄을 모아 최신순으로
 *   [cb_season style="hero|side|mobile"]   시즌 슬롯 (CB_SEASON 설정, 기간 지나면 숨김)
 *   [cb_side_brands]            사이드바 브랜드 목록 (보고 있는 글은 뺌)
 *   [cb_toc]                    사이드바 목차 (본문 목차를 그대로 복사)
 *
 * 그 밖에: 첫 화면은 사이드바·제목 없이, 사이드바 위젯 여백 20px, 푸터에 제휴 고지.
 */

if ( ! defined( 'ABSPATH' ) ) { return; }

/* ───────────────────────── 설정: 여기만 고치면 됩니다 ───────────────────────── */

/** 브랜드 목록 — 순서대로 표시. offer를 비우면 코드 데이터에서 할인율이 가장 큰 항목으로 자동 표시 */
function cbh_brands() {
	return array(
		array( 'slug' => 'yanolja',    'post' => 'yanolja-coupon',       'name' => '야놀자 (NOL)', 'short' => '야놀자',     'mono' => '야', 'tint' => '#ffe3ec', 'cat' => '국내숙소 · 티켓', 'offer' => '국내숙소 10만원 쿠폰팩 · KBO 포스트시즌 티켓', 'count' => '쿠폰 9종' ),
		array( 'slug' => 'agoda',      'post' => 'agoda-promo-code',     'name' => '아고다',       'short' => '아고다',     'mono' => '아', 'tint' => '#e3ecff', 'cat' => '해외숙소',        'offer' => '해외 특가 최대 30% · 마스터카드 최대 16%' ),
		array( 'slug' => 'tripcom',    'post' => 'tripcom-promo-code',   'name' => '트립닷컴',     'short' => '트립닷컴',   'mono' => '트', 'tint' => '#e3f1ff', 'cat' => '항공 · 숙소',     'offer' => '' ),
		array( 'slug' => 'expedia',    'post' => 'expedia-promo-code',   'name' => '익스피디아',   'short' => '익스피디아', 'mono' => '익', 'tint' => '#fff4d6', 'cat' => '해외숙소',        'offer' => 'KB국민·신한·현대·하나 카드 9%' ),
		array( 'slug' => 'hotelscom',  'post' => 'hotelscom-promo-code', 'name' => '호텔스닷컴',   'short' => '호텔스닷컴', 'mono' => '호', 'tint' => '#ffe6e1', 'cat' => '해외숙소',        'offer' => '' ),
		array( 'slug' => 'myrealtrip', 'post' => 'myrealtrip-coupon',    'name' => '마이리얼트립', 'short' => '마이리얼트립', 'mono' => '마', 'tint' => '#e2f7f0', 'cat' => '항공 · 투어',  'offer' => '항공 20% · 결제 최대 12만원' ),
		array( 'slug' => 'kkday',      'post' => 'kkday-promo-code',     'name' => 'KKday',        'short' => 'KKday',      'mono' => 'K',  'tint' => '#eaf8e3', 'cat' => '투어 · eSIM',     'offer' => '' ),
		array( 'slug' => 'klook',      'post' => 'klook-promo-code',     'name' => '클룩',         'short' => '클룩',       'mono' => '클', 'tint' => '#ffeedd', 'cat' => '투어 · 티켓',     'offer' => '' ),
	);
}

/** 시즌 슬롯 — until(이 날짜까지 표시)이 지나면 자동으로 숨김. 비우려면 title을 '' 로 */
function cbh_season() {
	return array(
		'title' => 'KBO 포스트시즌 티켓, NOL에서 단독 판매',
		'desc'  => '예매 규칙 · 취소표 확인법 · 원정 숙소',
		'label' => '예매 안내 보기',
		'url'   => '/yanolja-coupon/#kbo',
		'until' => '2026-11-10',
	);
}

/* ───────────────────────── 데이터 ───────────────────────── */

function cbh_today() { return function_exists( 'nolcs_today' ) ? nolcs_today() : wp_date( 'Y-m-d' ); }

function cbh_post( $slug ) {
	static $cache = array();
	if ( ! array_key_exists( $slug, $cache ) ) {
		$p = get_page_by_path( $slug, OBJECT, 'post' );
		$cache[ $slug ] = ( $p && $p->post_status === 'publish' ) ? $p : null;
	}
	return $cache[ $slug ];
}

function cbh_md( $ymd ) { return $ymd ? (int) substr( $ymd, 5, 2 ) . '.' . substr( $ymd, 8, 2 ) : ''; }

/** 브랜드 하나의 표시값: 코드 수, 검증일, 대표 혜택, 주소 */
function cbh_brand_data( $b ) {
	$p     = cbh_post( $b['post'] );
	$codes = function_exists( 'nolcs_get_codes' ) ? nolcs_get_codes( $b['slug'] ) : array();
	$ver   = '';
	$best  = null;
	foreach ( $codes as $m ) {
		if ( $m['verified'] > $ver ) { $ver = $m['verified']; }
		if ( $m['rate'] !== '' && ( ! $best || (float) $m['rate'] > (float) $best['rate'] ) ) { $best = $m; }
	}
	if ( $ver === '' && $p ) { $ver = substr( $p->post_modified, 0, 10 ); }
	$offer = $b['offer'] !== '' ? $b['offer'] : ( $best ? $best['title'] : '' );
	if ( isset( $b['count'] ) ) { $count = $b['count']; }
	else { $count = $codes ? '코드·혜택 ' . count( $codes ) . '개' : ''; }
	return array(
		'url'      => $p ? get_permalink( $p ) : '',
		'offer'    => $offer,
		'count'    => $count,
		'verified' => $ver,
	);
}

function cbh_last_verified() {
	$last = '';
	foreach ( cbh_brands() as $b ) {
		$d = cbh_brand_data( $b );
		if ( $d['url'] && $d['verified'] > $last ) { $last = $d['verified']; }
	}
	return $last;
}

/** 만료일이 오늘~days일 뒤인 코드, 만료일 순 */
function cbh_closing_items( $days ) {
	$items = array();
	$today = cbh_today();
	if ( ! function_exists( 'nolcs_get_codes' ) ) { return $items; }
	foreach ( cbh_brands() as $b ) {
		$p = cbh_post( $b['post'] );
		if ( ! $p ) { continue; }
		foreach ( nolcs_get_codes( $b['slug'] ) as $m ) {
			if ( $m['expire'] === '' || $m['expire'] < $today ) { continue; }
			$left = nolcs_days_left( $m['expire'] );
			if ( $left === null || $left > $days ) { continue; }
			$items[] = array( 'title' => $m['title'], 'brand' => $b['short'], 'expire' => $m['expire'], 'left' => $left, 'url' => get_permalink( $p ) );
		}
	}
	usort( $items, function ( $a, $b ) { return strcmp( $a['expire'], $b['expire'] ) ?: strcmp( $a['brand'], $b['brand'] ); } );
	return $items;
}

/** 각 글 '업데이트 사항' 목록의 첫 줄 */
function cbh_log_items() {
	$items = array();
	foreach ( cbh_brands() as $b ) {
		$p = cbh_post( $b['post'] );
		if ( ! $p ) { continue; }
		if ( ! preg_match( '#nol-updatelog__list[^>]*>.*?<li[^>]*>(.*?)</li>#s', $p->post_content, $m ) ) { continue; }
		$text = trim( wp_strip_all_tags( $m[1] ) );
		if ( ! preg_match( '/^(\d{4})년\s*(\d{1,2})월\s*(\d{1,2})일\s*[:：]\s*(.+)$/u', $text, $d ) ) { continue; }
		$items[] = array(
			'date'  => sprintf( '%04d-%02d-%02d', $d[1], $d[2], $d[3] ),
			'text'  => $d[4],
			'brand' => $b['short'],
			'url'   => get_permalink( $p ),
		);
	}
	usort( $items, function ( $a, $b ) { return strcmp( $b['date'], $a['date'] ); } );
	return $items;
}

function cbh_dday( $left ) { return $left === 0 ? 'D-DAY' : 'D-' . $left; }

/* ───────────────────────── 숏코드 ───────────────────────── */

add_shortcode( 'cb_last_verified', function () {
	$d = cbh_last_verified();
	return $d ? esc_html( str_replace( '-', '.', $d ) ) : '';
} );

add_shortcode( 'cb_brands', function () {
	$h = '<div class="cbh-brands">';
	foreach ( cbh_brands() as $b ) {
		$d = cbh_brand_data( $b );
		if ( ! $d['url'] ) { continue; }
		$meta = array_filter( array( $d['count'], $d['verified'] ? cbh_md( $d['verified'] ) . ' 확인' : '' ) );
		$h .= '<a class="cbh-brand" href="' . esc_url( $d['url'] ) . '">'
			. '<span class="cbh-brand__top"><span class="cbh-mono" style="background:' . esc_attr( $b['tint'] ) . '" aria-hidden="true">' . esc_html( $b['mono'] ) . '</span>'
			. '<span class="cbh-brand__name"><b>' . esc_html( $b['name'] ) . '</b><small>' . esc_html( $b['cat'] ) . '</small></span></span>'
			. '<span class="cbh-brand__offer">' . esc_html( $d['offer'] ) . '</span>'
			. '<span class="cbh-brand__foot"><span>' . esc_html( implode( ' · ', $meta ) ) . '</span><span class="cbh-more">코드 보기 →</span></span></a>';
	}
	return $h . '</div>';
} );

add_shortcode( 'cb_closing', function ( $atts ) {
	$a     = shortcode_atts( array( 'days' => '7', 'limit' => '4', 'style' => 'list' ), $atts, 'cb_closing' );
	$items = array_slice( cbh_closing_items( absint( $a['days'] ) ), 0, max( 1, absint( $a['limit'] ) ) );
	$cls   = $a['style'] === 'compact' ? 'cbh-closing cbh-closing--compact' : 'cbh-closing';
	if ( ! $items ) { return '<p class="cbh-empty">이번 주에 끝나는 혜택은 없습니다.</p>'; }
	$h = '<ul class="' . $cls . '">';
	foreach ( $items as $it ) {
		$hot = $it['left'] <= 2 ? ' is-hot' : '';
		$h  .= '<li><span class="cbh-dday' . $hot . '">' . esc_html( cbh_dday( $it['left'] ) ) . '</span>'
			. '<a class="cbh-closing__body" href="' . esc_url( $it['url'] ) . '"><b>' . esc_html( $it['title'] ) . '</b>'
			. '<small>' . esc_html( $it['brand'] . ' · ' . cbh_md( $it['expire'] ) . '까지' ) . '</small></a>'
			. ( $a['style'] === 'compact' ? '' : '<a class="cbh-go" href="' . esc_url( $it['url'] ) . '">바로 가기</a>' ) . '</li>';
	}
	return $h . '</ul>';
} );

add_shortcode( 'cb_log', function ( $atts ) {
	$a     = shortcode_atts( array( 'limit' => '3' ), $atts, 'cb_log' );
	$items = array_slice( cbh_log_items(), 0, max( 1, absint( $a['limit'] ) ) );
	if ( ! $items ) { return ''; }
	$h = '<ul class="cbh-log">';
	foreach ( $items as $it ) {
		$h .= '<li><span class="cbh-log__date">' . esc_html( cbh_md( $it['date'] ) ) . '</span>'
			. '<a href="' . esc_url( $it['url'] ) . '"><b>' . esc_html( $it['brand'] ) . '</b> · ' . esc_html( $it['text'] ) . '</a></li>';
	}
	return $h . '</ul>';
} );

add_shortcode( 'cb_season', function ( $atts ) {
	$a = shortcode_atts( array( 'style' => 'hero' ), $atts, 'cb_season' );
	$s = cbh_season();
	if ( $s['title'] === '' || ( $s['until'] !== '' && cbh_today() > $s['until'] ) ) { return ''; }
	$style = in_array( $a['style'], array( 'hero', 'side' ), true ) ? $a['style'] : 'hero';
	return '<a class="cbh-season cbh-season--' . $style . '" href="' . esc_url( home_url( $s['url'] ) ) . '">'
		. '<span class="cbh-season__tag">지금 뜨는 혜택</span>'
		. '<span class="cbh-season__title">' . esc_html( $s['title'] ) . '</span>'
		. '<span class="cbh-season__desc">' . esc_html( $s['desc'] ) . '</span>'
		. ( $style === 'hero' ? '<span class="cbh-season__btn">' . esc_html( $s['label'] ) . ' →</span>' : '' ) . '</a>';
} );

add_shortcode( 'cb_side_brands', function () {
	$current = is_singular( 'post' ) ? get_queried_object_id() : 0;
	$h = '<ul class="cbs-brands">';
	foreach ( cbh_brands() as $b ) {
		$p = cbh_post( $b['post'] );
		if ( ! $p || $p->ID === $current ) { continue; }
		$d  = cbh_brand_data( $b );
		$h .= '<li><a href="' . esc_url( $d['url'] ) . '"><span class="cbh-mono cbh-mono--s" style="background:' . esc_attr( $b['tint'] ) . '" aria-hidden="true">' . esc_html( $b['mono'] ) . '</span>'
			. '<span class="cbs-brands__t"><b>' . esc_html( $b['short'] ) . '</b><small>' . esc_html( $d['offer'] ) . '</small></span><span class="cbs-chev" aria-hidden="true">›</span></a></li>';
	}
	return $h . '</ul>';
} );

add_shortcode( 'cb_toc', function () {
	if ( ! is_singular( 'post' ) ) { return ''; }
	return '<nav class="cbs-toc" aria-label="이 글 목차" hidden><p class="cbs-h">이 글 목차</p><ol></ol></nav>'
		. '<script>(function(){var n=document.currentScript.previousElementSibling,o=n.querySelector("ol");'
		. 'var ls=document.querySelectorAll(".entry-content .nol-toc__list a[href^=\'#\']");'
		. 'if(!ls.length){ls=document.querySelectorAll(".entry-content h2[id]");}'
		. 'if(!ls.length){var w=n.closest(".widget");if(w)w.style.display="none";return;}[].forEach.call(ls,function(x){var li=document.createElement("li"),a=document.createElement("a");'
		. 'a.href=x.tagName==="A"?x.getAttribute("href"):"#"+x.id;a.textContent=x.textContent.trim();li.appendChild(a);o.appendChild(li);});n.hidden=false;})();</script>';
} );

/* ───────────────────────── 테마(GeneratePress) 연결 ───────────────────────── */

add_filter( 'generate_sidebar_layout', function ( $layout ) { return is_front_page() ? 'no-sidebar' : $layout; } );
add_filter( 'generate_show_title', function ( $show ) { return is_front_page() ? false : $show; } );
add_filter( 'generate_copyright', function () {
	return '© ' . wp_date( 'Y' ) . ' 쿠폰보관함 · 이 사이트는 제휴 링크를 포함하며, 링크를 통한 예약·구매 시 일정 수수료를 받을 수 있습니다. 이용자에게 추가 비용은 없습니다.';
} );

add_action( 'wp_head', function () {
	echo '<style id="cbh-css">' . cbh_css() . '</style>';
}, 30 );

function cbh_css() {
	return <<<'CSS'
:root{--cb-ink:#16151a;--cb-sub:#6b6875;--cb-body:#4a4754;--cb-line:#e8e6ee;--cb-soft:#faf9fc;--cb-brand:#d81b5f;--cb-ok:#0b7a3b}
.home .inside-article{background:transparent;padding:0 24px}
@media (max-width:768px){.home .inside-article{padding:0 16px}}
.home .site-content{padding-top:0}
.cbh{color:var(--cb-ink)}
.cbh a{text-decoration:none;color:inherit}
.cbh h2{margin:0;font-size:24px;font-weight:900;letter-spacing:-.5px;line-height:1.3}
.cbh-sec{margin:0 0 28px}
.cbh-sec__head{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;flex-wrap:wrap;margin:0 0 16px}
.cbh-sec__head p{margin:6px 0 0;font-size:15px;color:var(--cb-sub)}
.cbh-card{background:#fff;border:1px solid var(--cb-line);border-radius:20px;padding:24px}
.cbh-hero{display:flex;flex-wrap:wrap;gap:24px;align-items:stretch;padding:36px 0 8px;margin:0 0 28px}
.cbh-hero__main{flex:999 1 520px;min-width:0;display:flex;flex-direction:column;gap:16px;justify-content:center}
.cbh-badge{align-self:flex-start;display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:var(--cb-ok);background:#e7f6ee;border-radius:999px;padding:6px 12px;margin:0}
.cbh-badge:before{content:"";width:8px;height:8px;border-radius:50%;background:#12a150}
.cbh-hero h1{margin:0;font-size:42px;line-height:1.24;font-weight:900;letter-spacing:-1.2px}
.cbh-hero__lead{margin:0;font-size:17px;line-height:1.65;color:var(--cb-body);max-width:560px}
.cbh-search{display:flex;align-items:center;gap:8px;border:1px solid var(--cb-line);border-radius:999px;padding:0 6px 0 18px;height:50px;background:#fff;max-width:480px}
.cbh-search input{border:0;background:transparent;font:inherit;font-size:15px;flex:1;min-width:0;outline:none;padding:0;color:var(--cb-ink)}
.cbh-search button{width:40px;height:40px;border-radius:50%;border:0;background:var(--cb-ink);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;padding:0}
.cbh-chips{display:flex;flex-wrap:wrap;gap:8px}
.cbh-chips a{font-size:14px;font-weight:600;padding:10px 16px;border-radius:999px;background:#fff;border:1px solid var(--cb-line)}
.cbh-chips a:hover{border-color:var(--cb-ink)}
.cbh-season{display:flex;flex-direction:column;gap:10px;border-radius:20px;background:var(--cb-ink);color:#fff!important;padding:26px;text-decoration:none!important}
.cbh-season--hero{flex:1 1 320px;min-width:0;justify-content:center}
.cbh-season__tag{align-self:flex-start;font-size:12px;font-weight:700;background:#fff;color:var(--cb-ink);border-radius:6px;padding:4px 8px}
.cbh-season__title{font-size:24px;font-weight:900;line-height:1.35}
.cbh-season--side .cbh-season__title{font-size:17px}
.cbh-season__desc{font-size:14px;line-height:1.6;color:#d9d6e0}
.cbh-season__btn{align-self:flex-start;font-size:14px;font-weight:700;background:var(--cb-brand);border-radius:10px;padding:12px 16px;margin-top:4px}
.cbh-season--side{padding:18px;border-radius:16px}
.cbh-brands{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px}
.cbh-brand{background:#fff;border:1px solid var(--cb-line);border-radius:16px;padding:18px;display:flex;flex-direction:column;gap:12px;transition:border-color .15s,box-shadow .15s}
.cbh-brand:hover{border-color:#c9c5d3;box-shadow:0 6px 18px rgba(22,21,26,.06)}
.cbh-brand__top{display:flex;align-items:center;gap:12px}
.cbh-mono{flex:0 0 auto;width:44px;height:44px;border-radius:12px;color:var(--cb-ink);font-weight:900;font-size:18px;display:flex;align-items:center;justify-content:center}
.cbh-mono--s{width:30px;height:30px;border-radius:8px;font-size:13px}
.cbh-brand__name{display:flex;flex-direction:column;gap:2px;min-width:0}
.cbh-brand__name b{font-size:17px}
.cbh-brand__name small{font-size:13px;color:var(--cb-sub)}
.cbh-brand__offer{font-size:15px;font-weight:600;line-height:1.5;min-height:45px}
.cbh-brand__foot{display:flex;align-items:center;justify-content:space-between;gap:8px;border-top:1px solid #f0eef4;padding-top:12px;font-size:13px;color:var(--cb-sub)}
.cbh-more{font-weight:700;color:var(--cb-brand);white-space:nowrap}
.cbh-row{display:flex;flex-wrap:wrap;gap:20px;margin:0 0 28px}
.cbh-row>.cbh-card:first-child{flex:999 1 520px;min-width:0}
.cbh-row>.cbh-card:last-child{flex:1 1 320px;min-width:0}
.cbh-row h2{font-size:21px;margin:0 0 4px}
.cbh-sub{margin:0 0 14px;font-size:14px;color:var(--cb-sub)}
.cbh-closing{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:10px}
.cbh-closing li{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:var(--cb-soft);margin:0}
.cbh-dday{flex:0 0 auto;min-width:52px;text-align:center;font-size:13px;font-weight:900;color:#fff;background:var(--cb-ink);border-radius:8px;padding:6px 8px}
.cbh-dday.is-hot{background:var(--cb-brand)}
.cbh-closing__body{flex:1;min-width:0;display:flex;flex-direction:column;gap:2px}
.cbh-closing__body b{font-size:15px;line-height:1.4}
.cbh-closing__body small{font-size:13px;color:var(--cb-sub)}
.cbh-go{flex:0 0 auto;font-size:13px;font-weight:700;padding:10px 14px;border-radius:10px;background:var(--cb-ink);color:#fff!important}
.cbh-closing--compact li{padding:0;background:none;gap:10px}
.cbh-closing--compact .cbh-dday{min-width:44px;font-size:12px;padding:4px 6px}
.cbh-closing--compact .cbh-closing__body b{font-size:13px;font-weight:600}
.cbh-closing--compact .cbh-closing__body small{font-size:12px}
.cbh-empty{margin:0;font-size:14px;color:var(--cb-sub)}
.cbh-cats{display:flex;flex-direction:column;gap:16px}
.cbh-cats p{margin:0 0 8px;font-size:14px;font-weight:700;color:var(--cb-body)}
.cbh-cats span{display:flex;flex-wrap:wrap;gap:6px}
.cbh-cats a{font-size:13px;font-weight:600;padding:8px 12px;border-radius:999px;border:1px solid var(--cb-line);background:var(--cb-soft)}
.cbh-cats a:hover{border-color:var(--cb-ink)}
.cbh-how{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:22px}
.cbh-how h3{margin:4px 0 6px;font-size:16px;font-weight:700}
.cbh-how p{margin:0;font-size:14px;line-height:1.6;color:var(--cb-body)}
.cbh-step{font-size:13px;font-weight:900;color:var(--cb-brand)}
.cbh-log{list-style:none;margin:0;padding:0;background:#fff;border:1px solid var(--cb-line);border-radius:16px}
.cbh-log li{display:flex;gap:16px;align-items:baseline;padding:14px 20px;border-bottom:1px solid #f0eef4;font-size:15px;margin:0}
.cbh-log li:last-child{border-bottom:0}
.cbh-log__date{flex:0 0 auto;font-size:13px;font-weight:700;color:var(--cb-sub);min-width:44px}
.cbh-log a:hover{color:var(--cb-brand)}
@media (max-width:768px){
.cbh-hero{padding-top:16px}
.cbh-hero h1{font-size:28px;letter-spacing:-.8px}
.cbh-hero__lead{font-size:15px}
.cbh-brands{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
.cbh-brand{padding:14px;gap:8px}
.cbh-mono{width:32px;height:32px;font-size:14px;border-radius:9px}
.cbh-brand__name small{display:none}
.cbh-brand__name b{font-size:15px}
.cbh-brand__offer{font-size:13px;min-height:0}
.cbh-brand__foot{font-size:12px;flex-direction:column;align-items:flex-start;gap:2px;padding-top:8px}
.cbh-more{display:none}
.cbh-card{padding:18px}
.cbh h2{font-size:20px}
.cbh-go{display:none}
.cbh-log li{flex-direction:column;gap:2px;padding:12px 14px;font-size:14px}
}
#right-sidebar .widget{padding:20px}
#right-sidebar .widget:not(:has(*)){display:none}
@media (max-width:768px){#right-sidebar .widget:has(.cbs-toc){display:none}}
.cbs-wrap a{text-decoration:none;color:var(--cb-ink)}
.cbs-h{margin:0 0 10px;font-size:15px;font-weight:900;color:var(--cb-ink)}
.cbs-toc ol{margin:0;padding-left:18px;display:flex;flex-direction:column;gap:8px;font-size:14px;line-height:1.45}
.cbs-toc li{margin:0}
.cbs-toc a{color:var(--cb-ink);text-decoration:none}
.cbs-toc a:hover,.cbs-brands a:hover b{color:var(--cb-brand)}
.cbs-brands{list-style:none;margin:0;padding:0}
.cbs-brands li{border-top:1px solid #f0eef4;margin:0}
.cbs-brands li:first-child{border-top:0}
.cbs-brands a{display:flex;align-items:center;gap:10px;padding:10px 0;text-decoration:none;color:var(--cb-ink)}
.cbs-brands__t{flex:1;min-width:0;display:flex;flex-direction:column}
.cbs-brands__t b{font-size:14px}
.cbs-brands__t small{font-size:12px;color:var(--cb-sub);line-height:1.4}
.cbs-chev{color:var(--cb-sub)}
.cbs-ad{text-align:center;overflow:hidden}
.cbs-ad p{margin:0 0 6px;font-size:11px;line-height:1.5;color:var(--cb-sub)}
.cbs-ad iframe{display:block;margin:0 auto;max-width:none;border:0}
CSS;
}
