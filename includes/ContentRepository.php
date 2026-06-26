<?php
declare(strict_types=1);

class ContentRepository
{
    public function __construct(private PDO $pdo) {}

    public function getSettings(): array
    {
        $rows = $this->pdo->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[$row['setting_key']] = $row['setting_value'];
        }
        return $out;
    }

    public function saveSettings(array $data): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO site_settings (setting_key, setting_value) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );
        foreach ($data as $key => $value) {
            $stmt->execute(['k' => $key, 'v' => $value]);
        }
    }

    public function getNavItems(): array
    {
        return $this->pdo->query(
            'SELECT * FROM nav_items WHERE is_active = 1 ORDER BY sort_order ASC'
        )->fetchAll();
    }

    public function allNavItems(): array
    {
        return $this->pdo->query('SELECT * FROM nav_items ORDER BY sort_order ASC')->fetchAll();
    }

    public function saveNavItem(array $data, ?int $id = null): void
    {
        if ($id) {
            $sql = 'UPDATE nav_items SET label=:label, url_hash=:url_hash, sort_order=:sort_order, is_active=:is_active WHERE id=:id';
            $data['id'] = $id;
        } else {
            $sql = 'INSERT INTO nav_items (label, url_hash, sort_order, is_active) VALUES (:label, :url_hash, :sort_order, :is_active)';
        }
        $this->pdo->prepare($sql)->execute($data);
    }

    public function deleteNavItem(int $id): void
    {
        $this->pdo->prepare('DELETE FROM nav_items WHERE id = ?')->execute([$id]);
    }

    public function getPrograms(): array
    {
        return $this->pdo->query(
            'SELECT * FROM programs WHERE is_active = 1 ORDER BY sort_order ASC'
        )->fetchAll();
    }

    public function allPrograms(): array
    {
        return $this->pdo->query('SELECT * FROM programs ORDER BY sort_order ASC')->fetchAll();
    }

    public function saveProgram(array $data, ?int $id = null): void
    {
        if ($id) {
            $sql = 'UPDATE programs SET title=:title, description=:description, icon_class=:icon_class, sort_order=:sort_order, is_active=:is_active WHERE id=:id';
            $data['id'] = $id;
        } else {
            $sql = 'INSERT INTO programs (title, description, icon_class, sort_order, is_active) VALUES (:title, :description, :icon_class, :sort_order, :is_active)';
        }
        $this->pdo->prepare($sql)->execute($data);
    }

    public function deleteProgram(int $id): void
    {
        $this->pdo->prepare('DELETE FROM programs WHERE id = ?')->execute([$id]);
    }

    public function getStats(): array
    {
        return $this->pdo->query(
            'SELECT * FROM stats WHERE is_active = 1 ORDER BY sort_order ASC'
        )->fetchAll();
    }

    public function allStats(): array
    {
        return $this->pdo->query('SELECT * FROM stats ORDER BY sort_order ASC')->fetchAll();
    }

    public function saveStat(array $data, ?int $id = null): void
    {
        if ($id) {
            $sql = 'UPDATE stats SET value_text=:value_text, label=:label, sort_order=:sort_order, is_active=:is_active WHERE id=:id';
            $data['id'] = $id;
        } else {
            $sql = 'INSERT INTO stats (value_text, label, sort_order, is_active) VALUES (:value_text, :label, :sort_order, :is_active)';
        }
        $this->pdo->prepare($sql)->execute($data);
    }

    public function deleteStat(int $id): void
    {
        $this->pdo->prepare('DELETE FROM stats WHERE id = ?')->execute([$id]);
    }

    public function getTestimonials(): array
    {
        return $this->pdo->query(
            'SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC'
        )->fetchAll();
    }

    public function allTestimonials(): array
    {
        return $this->pdo->query('SELECT * FROM testimonials ORDER BY sort_order ASC')->fetchAll();
    }

    public function saveTestimonial(array $data, ?int $id = null): void
    {
        if ($id) {
            $sql = 'UPDATE testimonials SET name=:name, role_label=:role_label, quote_text=:quote_text, avatar=:avatar, sort_order=:sort_order, is_active=:is_active WHERE id=:id';
            $data['id'] = $id;
        } else {
            $sql = 'INSERT INTO testimonials (name, role_label, quote_text, avatar, sort_order, is_active) VALUES (:name, :role_label, :quote_text, :avatar, :sort_order, :is_active)';
        }
        $this->pdo->prepare($sql)->execute($data);
    }

    public function deleteTestimonial(int $id): void
    {
        $row = $this->pdo->prepare('SELECT avatar FROM testimonials WHERE id = ?');
        $row->execute([$id]);
        $avatar = $row->fetchColumn();
        delete_upload($avatar ?: null);
        $this->pdo->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
    }

    public function getGallery(string $type): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM gallery WHERE gallery_type = :t AND is_active = 1 ORDER BY sort_order ASC'
        );
        $stmt->execute(['t' => $type]);
        return $stmt->fetchAll();
    }

    public function allGallery(string $type): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM gallery WHERE gallery_type = :t ORDER BY sort_order ASC'
        );
        $stmt->execute(['t' => $type]);
        return $stmt->fetchAll();
    }

    public function saveGallery(array $data, ?int $id = null): void
    {
        if ($id) {
            $sql = 'UPDATE gallery SET gallery_type=:gallery_type, title=:title, image_path=:image_path, sort_order=:sort_order, is_active=:is_active WHERE id=:id';
            $data['id'] = $id;
        } else {
            $sql = 'INSERT INTO gallery (gallery_type, title, image_path, sort_order, is_active) VALUES (:gallery_type, :title, :image_path, :sort_order, :is_active)';
        }
        $this->pdo->prepare($sql)->execute($data);
    }

    public function deleteGallery(int $id): void
    {
        $row = $this->pdo->prepare('SELECT image_path FROM gallery WHERE id = ?');
        $row->execute([$id]);
        $img = $row->fetchColumn();
        delete_upload($img ?: null);
        $this->pdo->prepare('DELETE FROM gallery WHERE id = ?')->execute([$id]);
    }

    public function getArticles(int $limit = 3): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM articles WHERE is_active = 1 ORDER BY published_at DESC, id DESC LIMIT :lim'
        );
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function allArticles(): array
    {
        return $this->pdo->query(
            'SELECT * FROM articles ORDER BY published_at DESC, id DESC'
        )->fetchAll();
    }

    public function saveArticle(array $data, ?int $id = null): void
    {
        if ($id) {
            $sql = 'UPDATE articles SET title=:title, excerpt=:excerpt, category=:category, image_path=:image_path, link_url=:link_url, published_at=:published_at, is_active=:is_active WHERE id=:id';
            $data['id'] = $id;
        } else {
            $sql = 'INSERT INTO articles (title, excerpt, category, image_path, link_url, published_at, is_active) VALUES (:title, :excerpt, :category, :image_path, :link_url, :published_at, :is_active)';
        }
        $this->pdo->prepare($sql)->execute($data);
    }

    public function deleteArticle(int $id): void
    {
        $row = $this->pdo->prepare('SELECT image_path FROM articles WHERE id = ?');
        $row->execute([$id]);
        $img = $row->fetchColumn();
        delete_upload($img ?: null);
        $this->pdo->prepare('DELETE FROM articles WHERE id = ?')->execute([$id]);
    }

    public function getFooterLinks(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM footer_links
             WHERE is_active = 1
               AND url NOT IN ('tamu.php', 'undangan-portal.php')
             ORDER BY sort_order ASC"
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function allFooterLinks(): array
    {
        return $this->pdo->query('SELECT * FROM footer_links ORDER BY sort_order ASC')->fetchAll();
    }

    /** Tombol Buku Tamu & Undangan di landing page (default: tersembunyi). */
    public function getLandingAppButtons(): array
    {
        $s = $this->getSettings();
        $buttons = [];

        if (($s['show_buku_tamu'] ?? '0') === '1') {
            $buttons[] = [
                'key'   => 'buku_tamu',
                'label' => trim($s['buku_tamu_button_text'] ?? '') ?: 'Buku Tamu Digital',
                'url'   => 'tamu.php',
                'icon'  => 'bi-journal-check',
                'desc'  => trim($s['buku_tamu_desc'] ?? '') ?: 'Isi buku tamu digital untuk tamu pesantren.',
            ];
        }

        if (($s['show_undangan'] ?? '0') === '1') {
            $buttons[] = [
                'key'   => 'undangan',
                'label' => trim($s['undangan_button_text'] ?? '') ?: 'Undangan Digital',
                'url'   => 'undangan-portal.php',
                'icon'  => 'bi-envelope-heart',
                'desc'  => trim($s['undangan_desc'] ?? '') ?: 'Undangan digital acara haflah dan kegiatan pesantren.',
            ];
        }

        return $buttons;
    }

    public function isLandingAppVisible(string $app): bool
    {
        $s = $this->getSettings();
        $key = $app === 'undangan' ? 'show_undangan' : 'show_buku_tamu';

        return ($s[$key] ?? '0') === '1';
    }

    public function saveFooterLink(array $data, ?int $id = null): void
    {
        if ($id) {
            $sql = 'UPDATE footer_links SET label=:label, url=:url, sort_order=:sort_order, is_active=:is_active WHERE id=:id';
            $data['id'] = $id;
        } else {
            $sql = 'INSERT INTO footer_links (label, url, sort_order, is_active) VALUES (:label, :url, :sort_order, :is_active)';
        }
        $this->pdo->prepare($sql)->execute($data);
    }

    public function deleteFooterLink(int $id): void
    {
        $this->pdo->prepare('DELETE FROM footer_links WHERE id = ?')->execute([$id]);
    }

    public function getAdminByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
