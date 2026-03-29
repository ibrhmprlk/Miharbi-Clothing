<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @isset($singleOrder)
            Order #{{ $singleOrder->order_number }} - Miharbi Clothing
        @else
            My Orders - Miharbi Clothing
        @endisset
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --ink:     #0f172a;
            --paper:   #f8fafc;
            --accent:  #4f46e5;
            --accent2: #6366f1;
            --muted:   #64748b;
            --border:  rgba(226,232,240,0.8);
            --card:    #ffffff;
            --tag-bg:  #f1f5f9;
            --danger:  #dc2626;
            --danger-light: #fef2f2;
            --success: #059669;
            --warning: #d97706;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--paper); color: var(--ink); min-height: 100vh; }
        h1, h2, h3, .brand-text, .order-num { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }

        .site-header { position: sticky; top: 0; z-index: 50; background: rgba(255,255,255,0.95); backdrop-filter: blur(16px); border-bottom: 1px solid var(--border); transition: all 0.3s ease; }
        .site-header.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header-inner { max-width: 1200px; margin: 0 auto; padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; }
        .logo-mark { display: flex; align-items: center; gap: 10px; text-decoration: none; transition: transform 0.2s; }
        .logo-mark:hover { transform: scale(1.02); }
        .logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; box-shadow: 0 4px 14px rgba(79,70,229,0.35); }
        .logo-text { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 20px; color: var(--ink); letter-spacing: 1px; }
        .nav-links { display: flex; gap: 4px; align-items: center; }
        .nav-link { display: flex; align-items: center; gap: 6px; padding: 10px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; color: var(--muted); text-decoration: none; transition: all 0.2s; font-family: 'Plus Jakarta Sans', sans-serif; }
        .nav-link:hover { background: #eef2ff; color: #4f46e5; }
        .nav-link.active { background: #4f46e5; color: white; box-shadow: 0 4px 14px rgba(79,70,229,0.3); }

        .main-wrap { max-width: 1200px; margin: 0 auto; padding: 48px 24px; }

        .page-head { margin-bottom: 40px; display: flex; align-items: flex-end; justify-content: space-between; border-bottom: 1px solid var(--border); padding-bottom: 28px; }
        .page-title { font-size: clamp(32px, 5vw, 52px); font-weight: 800; color: var(--ink); line-height: 1; }
        .page-title span { color: var(--accent); }
        .page-sub { font-size: 14px; color: var(--muted); margin-top: 8px; }

        .orders-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 24px; }

        .order-card { background: var(--card); border: 1px solid var(--border); border-radius: 24px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.04); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; }
        .order-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(79,70,229,0.12); }
        .order-card.cancelled { opacity: 0.7; filter: grayscale(0.4); }

        .card-header { padding: 24px 24px 20px; display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border); background: linear-gradient(to right, #ffffff 0%, #f8fafc 100%); }
        .order-date-block .label { font-size: 11px; font-weight: 700; letter-spacing: 1.5px; color: var(--muted); text-transform: uppercase; }
        .order-date-block .date { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px; font-weight: 700; color: var(--ink); margin-top: 4px; }
        .order-num-badge { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 12px; font-weight: 700; color: var(--muted); letter-spacing: 1px; background: var(--tag-bg); padding: 6px 12px; border-radius: 20px; }

        .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 30px; font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; }
        .status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; opacity: 0.6; }
        .status-pending   { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .status-approved  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .status-shipped   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .status-delivered { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .status-cancelled { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        .items-strip { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; flex-direction: column; gap: 16px; }
        .item-row { display: flex; gap: 14px; align-items: flex-start; position: relative; }
        .item-thumb { width: 64px; height: 80px; border-radius: 12px; overflow: hidden; flex-shrink: 0; border: 1px solid var(--border); background: var(--tag-bg); transition: transform 0.2s; }
        .item-thumb:hover { transform: scale(1.05); }
        .item-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .item-thumb .no-img { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 20px; }
        .item-meta { flex: 1; }
        .item-brand { font-size: 10px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; color: var(--accent); margin-bottom: 3px; }
        .item-name { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700; color: var(--ink); line-height: 1.4; }
        .item-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
        .tag { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: var(--tag-bg); border: 1px solid var(--border); border-radius: 20px; font-size: 11px; font-weight: 600; color: var(--ink); transition: all 0.2s; }
        .tag:hover { background: #e2e8f0; }
        .color-dot { width: 10px; height: 10px; border-radius: 50%; border: 1.5px solid rgba(255,255,255,0.6); box-shadow: 0 0 0 1px var(--border); flex-shrink: 0; }
        .tag-sku { font-family: monospace; font-size: 10px; color: var(--muted); background: transparent; border-color: transparent; padding-left: 0; }
        .tag-qty { background: #e0e7ff; color: var(--accent); font-weight: 700; }
        .item-price { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700; color: var(--accent); white-space: nowrap; }
        .more-items { font-size: 12px; color: var(--muted); font-weight: 600; text-align: center; padding: 10px 0 4px; background: var(--tag-bg); border-radius: 10px; margin-top: 4px; }

        .card-footer { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
        .total-block .label { font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .total-block .amount { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 24px; font-weight: 800; color: var(--ink); }
        .btn-detail { display: inline-flex; align-items: center; gap: 8px; background: var(--ink); color: white; padding: 12px 24px; border-radius: 30px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; box-shadow: 0 4px 14px rgba(15,23,42,0.15); }
        .btn-detail:hover { background: var(--accent); transform: scale(1.05); box-shadow: 0 6px 20px rgba(79,70,229,0.3); }

        .btn-cancel-item { display: inline-flex; align-items: center; gap: 4px; background: transparent; color: var(--danger); border: 1.5px solid #fecaca; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.2s; font-family: 'Plus Jakarta Sans', sans-serif; margin-top: 8px; }
        .btn-cancel-item:hover { background: var(--danger-light); border-color: var(--danger); transform: scale(1.02); }

        .empty-state { grid-column: 1 / -1; text-align: center; padding: 100px 24px; background: var(--card); border-radius: 24px; border: 2px dashed var(--border); }
        .empty-icon { width: 100px; height: 100px; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #94a3b8; margin: 0 auto 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .empty-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 32px; font-weight: 800; color: var(--ink); margin-bottom: 12px; }
        .empty-sub { font-size: 16px; color: var(--muted); margin-bottom: 32px; }
        .btn-explore { display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, var(--accent) 0%, var(--accent2) 100%); color: white; padding: 16px 32px; border-radius: 30px; font-size: 15px; font-weight: 700; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 20px rgba(79,70,229,0.3); }
        .btn-explore:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(79,70,229,0.4); }

        /* DETAIL PAGE */
        .back-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; padding: 20px 24px; background: var(--card); border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        .back-link { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: var(--muted); text-decoration: none; transition: all 0.2s; padding: 8px 16px; border-radius: 12px; }
        .back-link:hover { color: var(--accent); background: #eef2ff; }
        .detail-order-num { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700; color: var(--muted); letter-spacing: 1px; background: var(--tag-bg); padding: 8px 16px; border-radius: 20px; }

        .detail-grid { display: grid; grid-template-columns: 1fr 380px; gap: 28px; align-items: start; }
        @media (max-width: 1024px) { .detail-grid { grid-template-columns: 1fr; } }

        .dcard { background: var(--card); border: 1px solid var(--border); border-radius: 24px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.04); transition: box-shadow 0.3s; }
        .dcard:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .dcard-title { padding: 20px 24px; border-bottom: 1px solid var(--border); font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); display: flex; align-items: center; gap: 10px; background: linear-gradient(to right, #f8fafc 0%, #ffffff 100%); }
        .dcard-title i { color: var(--accent); font-size: 18px; }

        .detail-item { display: flex; gap: 20px; padding: 24px; border-bottom: 1px solid var(--border); align-items: flex-start; transition: all 0.2s; position: relative; }
        .detail-item:last-child { border-bottom: none; }
        .detail-item:hover { background: #f8fafc; }

        .det-img { width: 100px; height: 130px; border-radius: 16px; overflow: hidden; flex-shrink: 0; border: 1px solid var(--border); background: var(--tag-bg); transition: transform 0.3s; }
        .det-img:hover { transform: scale(1.03); }
        .det-img img { width: 100%; height: 100%; object-fit: cover; }
        .det-img .no-img { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 28px; }
        .det-info { flex: 1; }
        .det-brand { font-size: 10px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: var(--accent); margin-bottom: 4px; }
        .det-name { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 18px; font-weight: 700; color: var(--ink); line-height: 1.4; }
        .det-collection { font-size: 12px; color: var(--muted); margin-top: 4px; font-weight: 500; }
        .det-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 12px; }
        .det-price-block { text-align: right; flex-shrink: 0; }
        .det-price { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 22px; font-weight: 800; color: var(--accent); }
        .det-qty { font-size: 12px; color: var(--muted); margin-top: 4px; font-weight: 600; }

        .invoice-rows { padding: 24px; }
        .inv-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; font-size: 14px; color: var(--muted); border-bottom: 1px dashed var(--border); }
        .inv-row:last-child { border-bottom: none; }
        .inv-row .val { font-weight: 700; color: var(--ink); }
        .inv-total-row { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-top: 2px solid var(--ink); background: linear-gradient(135deg, var(--ink) 0%, #1e293b 100%); color: white; }
        .inv-total-row .lbl { font-size: 13px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .inv-total-row .amount { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 32px; font-weight: 800; }

        .timeline-wrap { padding: 28px; }
        .tl-item { display: flex; gap: 16px; padding-bottom: 28px; position: relative; }
        .tl-item:last-child { padding-bottom: 0; }
        .tl-left { display: flex; flex-direction: column; align-items: center; }
        .tl-dot { width: 14px; height: 14px; border-radius: 50%; background: var(--border); border: 3px solid white; box-shadow: 0 0 0 2px var(--border); flex-shrink: 0; z-index: 2; transition: all 0.3s; }
        .tl-dot.done { background: var(--accent); box-shadow: 0 0 0 4px #e0e7ff; transform: scale(1.1); }
        .tl-line { width: 2px; flex: 1; min-height: 30px; background: var(--border); margin-top: 4px; }
        .tl-item:last-child .tl-line { display: none; }
        .tl-label { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700; color: var(--ink); }
        .tl-label.muted { color: var(--muted); }
        .tl-sub { font-size: 13px; color: var(--muted); margin-top: 4px; font-weight: 500; }
        .tl-tracking { font-size: 12px; color: var(--accent); font-weight: 700; margin-top: 6px; display: inline-flex; align-items: center; gap: 6px; background: #eef2ff; padding: 6px 12px; border-radius: 20px; }

        .addr-wrap { padding: 28px; }
        .addr-name { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 18px; font-weight: 700; color: var(--ink); margin-bottom: 8px; }
        .addr-line { font-size: 14px; color: var(--muted); line-height: 1.8; }
        .addr-city { font-weight: 700; color: var(--ink); }
        .addr-contact { margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); }
        .addr-contact-lbl { font-size: 11px; color: var(--muted); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .addr-phone { font-size: 16px; font-weight: 700; color: var(--ink); margin-top: 6px; }

        .btn-cancel-detail-item { display: inline-flex; align-items: center; gap: 8px; background: transparent; color: var(--danger); border: 2px solid #fecaca; padding: 8px 16px; border-radius: 12px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s; font-family: 'Plus Jakarta Sans', sans-serif; margin-top: 12px; }
        .btn-cancel-detail-item:hover { background: var(--danger-light); border-color: var(--danger); transform: scale(1.02); }

        .pagination-wrap { margin-top: 48px; display: flex; justify-content: center; }

        .scroll-top { position: fixed; bottom: 30px; right: 30px; width: 56px; height: 56px; background: linear-gradient(135deg, var(--accent) 0%, var(--accent2) 100%); color: white; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 8px 30px rgba(79,70,229,0.4); opacity: 0; visibility: hidden; transform: translateY(20px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); z-index: 100; }
        .scroll-top.visible { opacity: 1; visibility: visible; transform: translateY(0); }
        .scroll-top:hover { transform: translateY(-4px) scale(1.1); box-shadow: 0 12px 40px rgba(79,70,229,0.5); }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .order-card { animation: fadeUp 0.5s ease both; }
        .order-card:nth-child(1)  { animation-delay: 0.00s; }
        .order-card:nth-child(2)  { animation-delay: 0.06s; }
        .order-card:nth-child(3)  { animation-delay: 0.12s; }
        .order-card:nth-child(4)  { animation-delay: 0.18s; }
        .order-card:nth-child(5)  { animation-delay: 0.24s; }
        .order-card:nth-child(6)  { animation-delay: 0.30s; }

        /* REVIEW STYLES */
        .review-section { margin-top: 24px; padding: 24px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 20px; border: 1px solid var(--border); }
        .review-section-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 800; color: var(--ink); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .review-section-title i { color: var(--warning); font-size: 20px; }

        /*
         * ════════════════════════════════════════
         * STAR RATING — DÜZELTME
         *
         * Problem: Her input hemen label'ından önce
         * yazılınca (input5,label5,input4,label4...)
         * "input:checked ~ label" sadece bir sonraki
         * kardeşe etki eder, tüm label'lara değil.
         *
         * Çözüm: flex-direction:row-reverse + input/label
         * çiftleri 5→1 sırasıyla ama input HER ZAMAN
         * label'dan önce. CSS ~ selector DOM'da
         * checked input'tan sonra gelen TÜM kardeşleri
         * seçer. row-reverse ile görsel sıra 1→2→3→4→5.
         *
         * Örnek: input[val=3]:checked ~ label →
         * label[for=star-2] ve label[for=star-1] sarı,
         * bunlar görsel olarak solda = 3 yıldız ✓
         * ════════════════════════════════════════
         */
        .star-rating {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 4px;
            margin-bottom: 16px;
        }
        .star-rating input[type="radio"] {
            display: none;
        }
        .star-rating label {
            cursor: pointer;
            font-size: 28px;
            color: #e2e8f0;
            transition: color 0.15s, transform 0.15s;
            line-height: 1;
        }
        /* Checked input'tan sonraki tüm kardeş label'lar = daha küçük değerliler = sarı */
        .star-rating input[type="radio"]:checked ~ label {
            color: #fbbf24;
        }
        /* Hover: üzerine gelinen ve DOM'da sonra gelenler (= görsel solundakiler) */
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #fbbf24;
            transform: scale(1.15);
        }

        .star-display { display: flex; gap: 2px; font-size: 16px; }

        .review-textarea { width: 100%; min-height: 120px; padding: 16px; border: 2px solid var(--border); border-radius: 16px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; resize: vertical; transition: all 0.2s; background: white; }
        .review-textarea:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

        .btn-submit-review { display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, var(--accent) 0%, var(--accent2) 100%); color: white; padding: 14px 28px; border-radius: 30px; font-size: 14px; font-weight: 700; border: none; cursor: pointer; transition: all 0.3s; margin-top: 16px; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3); }
        .btn-submit-review:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4); }
        .btn-submit-review:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .existing-review { background: white; padding: 20px; border-radius: 16px; border: 1px solid var(--border); margin-top: 16px; }
        .review-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .reviewer-info { display: flex; align-items: center; gap: 12px; }
        .reviewer-avatar { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, var(--accent) 0%, var(--accent2) 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 16px; }
        .reviewer-meta { display: flex; flex-direction: column; }
        .reviewer-name { font-weight: 700; font-size: 14px; color: var(--ink); }
        .review-date { font-size: 12px; color: var(--muted); }
        .review-verified { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--success); font-weight: 700; background: #ecfdf5; padding: 4px 10px; border-radius: 20px; }
        .review-comment { font-size: 14px; line-height: 1.7; color: var(--ink); margin-top: 12px; }

        .review-alert { display: flex; align-items: center; gap: 12px; padding: 16px 20px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; margin-bottom: 20px; font-size: 13px; color: #92400e; }
        .review-alert i { font-size: 20px; color: #f59e0b; }

        @media (max-width: 768px) {
            .orders-grid { grid-template-columns: 1fr; }
            .header-inner { padding: 12px 16px; }
            .main-wrap { padding: 24px 16px; }
            .page-head { flex-direction: column; align-items: flex-start; gap: 16px; }
            .detail-grid { gap: 20px; }
            .card-footer { flex-direction: column; gap: 16px; align-items: stretch; }
            .btn-detail { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body x-data="{ scrollTop: false, mobileMenu: false, activeFilter: 'all' }" @scroll.window="scrollTop = window.pageYOffset > 300">

    <header class="site-header" :class="{ 'scrolled': scrollTop }">
        <div class="header-inner">
            <a href="{{ url('/dashboard') }}" class="logo-mark">
                <div class="logo-icon"><i class="bi bi-shop"></i></div>
                <span class="logo-text">Miharbi Clothing</span>
            </a>
            <nav class="nav-links hidden md:flex">
                <a href="{{ url('/dashboard') }}" class="nav-link"><i class="bi bi-grid-1x2"></i> Dashboard</a>
                <a href="{{ url('favorites') }}" class="nav-link"><i class="bi bi-heart"></i> Favorites</a>
                <a href="{{ url('mycart') }}" class="nav-link"><i class="bi bi-cart3"></i> My Cart</a>
                <a href="{{ url('myorders') }}" class="nav-link"><i class="bi bi-box-seam"></i> My Orders</a>
                @auth
                    <div class="relative ml-2" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-100 transition-all duration-200">
                            <div class="w-9 h-9 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-indigo-600">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down text-xs text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-sm font-bold text-slate-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                                <i class="bi bi-person-gear text-lg"></i> Profile Settings
                            </a>
                            <div class="border-t border-slate-100 mt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                                        <i class="bi bi-box-arrow-right text-lg"></i> Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
            </nav>
            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-xl hover:bg-slate-100 transition">
                <i class="bi text-2xl" :class="mobileMenu ? 'bi-x-lg' : 'bi-list'"></i>
            </button>
        </div>
        <div x-show="mobileMenu" x-cloak @click.away="mobileMenu = false"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t bg-white px-4 py-4 space-y-2">
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 font-semibold" @click="mobileMenu = false"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <a href="{{ url('favorites') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 font-semibold" @click="mobileMenu = false"><i class="bi bi-heart"></i> Favorites</a>
            <a href="{{ url('mycart') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 font-semibold" @click="mobileMenu = false"><i class="bi bi-cart3"></i> My Cart</a>
            <a href="{{ url('myorders') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 font-semibold" @click="mobileMenu = false"><i class="bi bi-box-seam"></i> My Orders</a>
            @auth
                <div class="border-t pt-4 mt-4">
                    <div class="flex items-center gap-3 px-4 py-2 mb-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-indigo-600">
                            <i class="bi bi-person-fill text-lg"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 font-semibold" @click="mobileMenu = false"><i class="bi bi-person-gear"></i> Profile Settings</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-50 text-red-600 font-semibold text-left" @click="mobileMenu = false"><i class="bi bi-box-arrow-right"></i> Log Out</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <main class="main-wrap">

        @isset($singleOrder)

            <div class="back-bar">
                <a href="{{ route('myorders') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to My Orders</a>
                @php $firstSku = $singleOrder->items->first()?->variant?->sku; @endphp
                <span class="detail-order-num">{{ $firstSku ? 'SKU: '.$firstSku : 'Order #'.$singleOrder->order_number }}</span>
            </div>

            <div class="detail-grid">
                <div style="display:flex;flex-direction:column;gap:24px;">

                    <div class="dcard">
                        <div class="dcard-title"><i class="bi bi-collection"></i> Package Contents</div>

                        @foreach($singleOrder->items as $item)
                            @php
                                $variant        = $item->variant;
                                $image          = $variant->images->first();
                                $imageUrl       = $image ? (str_starts_with($image->image_url,'http') ? $image->image_url : asset('storage/'.$image->image_url)) : null;
                                $existingReview = $item->review;
                                $canReview      = $singleOrder->status === 'delivered' && !$existingReview;
                                $canCancel      = $singleOrder->status === 'pending' && $item->status !== 'cancelled';
                            @endphp

                            <div class="detail-item" style="flex-direction:column;gap:20px;">
                                <div style="display:flex;gap:20px;width:100%;">
                                    <div class="det-img">
                                        @if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $item->product_name }}">
                                        @else<div class="no-img"><i class="bi bi-image"></i></div>@endif
                                    </div>
                                    <div class="det-info">
                                        <div class="det-brand">{{ $variant->brand ?? 'Miharbi Exclusive' }}</div>
                                        <div class="det-name">{{ $item->product_name }}</div>
                                        @if($variant->collection)<div class="det-collection">{{ $variant->collection }}</div>@endif
                                        <div class="det-tags">
                                            @if($variant->color)
                                                <span class="tag"><span class="color-dot" style="background:{{ $variant->color_code ?? '#ccc' }}"></span>{{ $variant->color }}</span>
                                            @endif
                                            @if($variant->size)<span class="tag"><i class="bi bi-rulers" style="font-size:10px;opacity:.6"></i> {{ $variant->size }}</span>@endif
                                            @if($variant->sku)<span class="tag tag-sku">SKU: {{ $variant->sku }}</span>@endif
                                            <span class="tag tag-qty"><i class="bi bi-bag"></i> Qty: {{ $item->quantity }}</span>
                                          @if($item->return_status !== 'none')
                                                <span class="tag" style="background:#fef2f2;color:#dc2626;border-color:#fecaca;"><i class="bi bi-x-circle"></i> Cancelled</span>
                                            @endif
                                        </div>
                                        @if($canCancel)
                                            <form action="{{ route('myorders.cancel.item', $item->id) }}" method="POST" onsubmit="return confirm('Cancel {{ $item->product_name }}?');" style="margin:0;">
                                                @csrf
                                                <button type="submit" class="btn-cancel-detail-item"><i class="bi bi-x-circle-fill"></i> Cancel This Item</button>
                                            </form>
                                        @endif
                                    </div>
                                  <div class="det-price-block">
    <div class="det-price">{{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }} ₺</div>
    <div class="det-qty">{{ $item->quantity }} x {{ number_format($item->unit_price, 2, ',', '.') }} ₺</div>
</div>
                                </div>

                                @if($item->status !== 'cancelled')
                                    <div style="width:100%;border-top:1px solid var(--border);padding-top:20px;">

                                        @if($existingReview)
                                            {{-- Mevcut yorum gösterimi --}}
                                            <div class="existing-review">
                                                <div class="review-header">
                                                    <div class="reviewer-info">
                                                        <div class="reviewer-avatar">{{ substr(Auth::user()->name,0,1) }}</div>
                                                        <div class="reviewer-meta">
                                                            <span class="reviewer-name">{{ Auth::user()->name }}</span>
                                                            <span class="review-date">{{ $existingReview->created_at->format('d M Y') }}</span>
                                                        </div>
                                                    </div>
                                                    <span class="review-verified"><i class="bi bi-check-circle-fill"></i> Verified Purchase</span>
                                                </div>
                                                {{-- Yıldız gösterimi: düz for 1→5, inline style ile sarı/gri --}}
                                                <div class="star-display">
                                                    @for($s = 1; $s <= 5; $s++)
                                                        <i class="bi bi-star-fill" style="color:{{ $s <= $existingReview->rating ? '#fbbf24' : '#e2e8f0' }}"></i>
                                                    @endfor
                                                </div>
                                                @if($existingReview->comment)
                                                    <p class="review-comment">{{ $existingReview->comment }}</p>
                                                @endif
                                            </div>

                                        @elseif($canReview)
                                            {{-- Yorum formu --}}
                                            <div x-data="{ open: false, mainRating: 0 }">
                                                <button type="button" @click="open = !open" class="btn-submit-review" style="margin-top:0;">
                                                    <i class="bi bi-star"></i>
                                                    <span x-text="open ? 'Close Review' : 'Write a Review'"></span>
                                                </button>

                                                <div x-show="open" x-cloak style="margin-top:20px;">
                                                    <form action="{{ route('reviews.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="urun_id" value="{{ $variant->urun_id }}">
                                                        <input type="hidden" name="urun_variant_id" value="{{ $variant->id }}">
                                                        <input type="hidden" name="order_item_id" value="{{ $item->id }}">

                                                        <div class="review-section">
                                                            <div class="review-section-title">
                                                                <i class="bi bi-star-fill"></i> Your Rating
                                                            </div>

                                                            {{--
                                                                DOM sırası: 5→1 (input + label çifti)
                                                                CSS row-reverse → görsel: 1 2 3 4 5
                                                                input[val=3]:checked ~ label → label2, label1 sarı
                                                                row-reverse ile bunlar görsel SOLDA = 3 yıldız ✓
                                                            --}}
                                                            <div class="star-rating">
                                                                @for($i = 5; $i >= 1; $i--)
                                                                    <input type="radio"
                                                                           id="star-{{ $item->id }}-{{ $i }}"
                                                                           name="rating"
                                                                           value="{{ $i }}"
                                                                           x-model="mainRating"
                                                                           required>
                                                                    <label for="star-{{ $item->id }}-{{ $i }}">
                                                                        <i class="bi bi-star-fill"></i>
                                                                    </label>
                                                                @endfor
                                                            </div>

                                                            <textarea name="comment"
                                                                      class="review-textarea"
                                                                      placeholder="Share your experience with this product..."
                                                                      required
                                                                      minlength="10"></textarea>

                                                            <div class="review-alert">
                                                                <i class="bi bi-info-circle"></i>
                                                                <span>Your review will be published after moderation.</span>
                                                            </div>

                                                            <button type="submit" class="btn-submit-review" :disabled="mainRating == 0">
                                                                <i class="bi bi-send-fill"></i> Submit Review
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                        @elseif($singleOrder->status !== 'delivered')
                                            <div style="padding:16px;background:#f1f5f9;border-radius:12px;display:flex;align-items:center;gap:10px;color:var(--muted);font-size:13px;">
                                                <i class="bi bi-clock"></i>
                                                <span>You can review this product after delivery</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Invoice --}}
                    <div class="dcard">
                        <div class="dcard-title"><i class="bi bi-receipt"></i> Invoice Summary</div>
                        <div class="invoice-rows">
                            <div class="inv-row"><span>Subtotal</span><span class="val">{{ number_format($singleOrder->subtotal,2,',','.') }} ₺</span></div>
                            <div class="inv-row">
                                <span>Shipping Cost</span>
                                <span class="val" style="color:#059669">{{ $singleOrder->shipping_cost > 0 ? number_format($singleOrder->shipping_cost,2,',','.').' ₺' : 'Free' }}</span>
                            </div>
                            <div class="inv-row">
    <span>VAT (18%)</span>
    <span class="val">{{ number_format($singleOrder->tax_amount, 2, ',', '.') }} ₺</span>
</div>
                            @if(($singleOrder->discount_amount ?? 0) > 0)
                                <div class="inv-row"><span>Discount</span><span class="val" style="color:#dc2626">-{{ number_format($singleOrder->discount_amount,2,',','.') }} ₺</span></div>
                            @endif
                        </div>
                        <div class="inv-total-row">
                            <span class="lbl">Total Payment</span>
                            <span class="amount">{{ number_format($singleOrder->total,2,',','.') }} ₺</span>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN --}}
                <div style="display:flex;flex-direction:column;gap:24px;">
                    <div class="dcard">
                        <div class="dcard-title"><i class="bi bi-truck"></i> Order Status</div>
                        <div class="timeline-wrap">
                            <div class="tl-item">
                                <div class="tl-left"><div class="tl-dot done"></div><div class="tl-line"></div></div>
                                <div class="tl-content"><div class="tl-label">Order Received</div><div class="tl-sub">{{ $singleOrder->created_at->format('d M Y, H:i') }}</div></div>
                            </div>
                            <div class="tl-item">
                                <div class="tl-left"><div class="tl-dot {{ in_array($singleOrder->status,['approved','processing','shipped','delivered']) ? 'done' : '' }}"></div><div class="tl-line"></div></div>
                                <div class="tl-content"><div class="tl-label {{ !in_array($singleOrder->status,['approved','processing','shipped','delivered']) ? 'muted' : '' }}">Order Approved</div></div>
                            </div>
                            <div class="tl-item">
                                <div class="tl-left"><div class="tl-dot {{ in_array($singleOrder->status,['shipped','delivered']) ? 'done' : '' }}"></div><div class="tl-line"></div></div>
                                <div class="tl-content">
                                    <div class="tl-label {{ !in_array($singleOrder->status,['shipped','delivered']) ? 'muted' : '' }}">Shipped</div>
                                    @if($singleOrder->shipped_at)<div class="tl-sub">{{ $singleOrder->shipped_at->format('d M Y, H:i') }}</div>@endif
                                    @if($singleOrder->tracking_number)<div class="tl-tracking"><i class="bi bi-qr-code-scan"></i> {{ $singleOrder->tracking_number }}</div>@endif
                                </div>
                            </div>
                            <div class="tl-item">
                                <div class="tl-left"><div class="tl-dot {{ $singleOrder->status === 'delivered' ? 'done' : '' }}"></div></div>
                                <div class="tl-content">
                                    <div class="tl-label {{ $singleOrder->status !== 'delivered' ? 'muted' : '' }}">Delivered</div>
                                    @if($singleOrder->delivered_at)<div class="tl-sub">{{ $singleOrder->delivered_at->format('d M Y, H:i') }}</div>@endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dcard">
                        <div class="dcard-title"><i class="bi bi-geo-alt"></i> Shipping Address</div>
                        <div class="addr-wrap">
                            <div class="addr-name">{{ $singleOrder->shipping_full_name }}</div>
                            <div class="addr-line">
                                {{ $singleOrder->shipping_address }}<br>
                                <span class="addr-city">{{ $singleOrder->shipping_district }} / {{ $singleOrder->shipping_city }}</span><br>
                                {{ $singleOrder->shipping_zip }}
                            </div>
                            <div class="addr-contact">
                                <div class="addr-contact-lbl">Contact</div>
                                <div class="addr-phone"><i class="bi bi-telephone-fill mr-2"></i>{{ $singleOrder->shipping_phone }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else

            <div class="page-head">
                <div>
                    <h1 class="page-title">My <span>Orders</span></h1>
                    <p class="page-sub">Track all your purchases and manage your orders here.</p>
                </div>
                @if($orders->total() > 0)
                    <span style="font-size:14px;color:var(--muted);font-weight:700;background:var(--tag-bg);padding:8px 16px;border-radius:20px;">{{ $orders->total() }} orders</span>
                @endif
            </div>

            <div class="orders-grid">
                @forelse($orders as $order)
                    <div class="order-card {{ $order->status === 'cancelled' ? 'cancelled' : '' }}" x-show="activeFilter === 'all' || activeFilter === '{{ $order->status }}'">
                        <div class="card-header">
                            <div class="order-date-block">
                                <div class="label">Order Date</div>
                                <div class="date">{{ $order->created_at->format('d M Y') }}</div>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px;">
                                <span class="status-pill status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                @php $sku = $order->items->first()?->variant?->sku; @endphp
                                <span class="order-num-badge">{{ $sku ? $sku : '#'.$order->order_number }}</span>
                            </div>
                        </div>

                        <div class="items-strip">
                            @foreach($order->items->take(3) as $item)
                                @php
                                    $v   = $item->variant;
                                    $img = $v->images->first();
                                    $src = $img ? (str_starts_with($img->image_url,'http') ? $img->image_url : asset('storage/'.$img->image_url)) : null;
                                @endphp
                                <div class="item-row">
                                    <div class="item-thumb">
                                        @if($src)<img src="{{ $src }}" alt="{{ $item->product_name }}">
                                        @else<div class="no-img"><i class="bi bi-image"></i></div>@endif
                                    </div>
                                    <div class="item-meta">
                                        @if($v->brand)<div class="item-brand">{{ $v->brand }}</div>@endif
                                        <div class="item-name">{{ $item->product_name }}</div>
                                        <div class="item-tags">
                                            @if($v->color)<span class="tag"><span class="color-dot" style="background:{{ $v->color_code ?? '#ccc' }}"></span>{{ $v->color }}</span>@endif
                                            @if($v->size)<span class="tag">{{ $v->size }}</span>@endif
                                            @if($v->collection)<span class="tag" style="color:var(--muted)">{{ $v->collection }}</span>@endif
                                            @if($v->sku)<span class="tag tag-sku">{{ $v->sku }}</span>@endif
                                            <span class="tag tag-qty"><i class="bi bi-bag"></i> Qty: {{ $item->quantity }}</span>
                                            @if($item->status === 'cancelled')<span class="tag" style="background:#fef2f2;color:#dc2626;border-color:#fecaca;"><i class="bi bi-x-circle"></i> Cancelled</span>@endif
                                        </div>
                                        @if($order->status === 'pending' && $item->status !== 'cancelled')
                                            <form action="{{ route('myorders.cancel.item', $item->id) }}" method="POST" onsubmit="return confirm('Cancel {{ $item->product_name }}?');" style="margin:0;">
                                                @csrf
                                                <button type="submit" class="btn-cancel-item"><i class="bi bi-x-circle"></i> Cancel Item</button>
                                            </form>
                                        @endif
                                    </div>
                                  <div class="item-price">
    {{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }} ₺
    <div style="font-size:11px;color:var(--muted);font-weight:600;margin-top:2px;">{{ $item->quantity }} x {{ number_format($item->unit_price,2,',','.') }} ₺</div>
</div>
                                </div>
                            @endforeach
                            @if($order->items->count() > 3)
                                <div class="more-items"><i class="bi bi-plus-circle mr-1"></i> {{ $order->items->count() - 3 }} more items</div>
                            @endif
                        </div>

                        <div class="card-footer">
                            <div class="total-block">
                                <div class="label">Total</div>
                                <div class="amount">{{ number_format($order->total,2,',','.') }} ₺</div>
                            </div>
                            <a href="{{ route('myorders.show', $order->id) }}" class="btn-detail">Details <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="bi bi-cart-x"></i></div>
                        <h3 class="empty-title">No Orders Yet</h3>
                        <p class="empty-sub">Start shopping now and discover amazing products.</p>
                        <a href="{{ route('dashboard') }}" class="btn-explore">Explore Products <i class="bi bi-arrow-right"></i></a>
                    </div>
                @endforelse
            </div>

            <div class="pagination-wrap">{{ $orders->links() }}</div>

        @endisset
    </main>

    <button @click="window.scrollTo({top:0,behavior:'smooth'})" class="scroll-top" :class="{'visible':scrollTop}">
        <i class="bi bi-arrow-up"></i>
    </button>

</body>
</html>
