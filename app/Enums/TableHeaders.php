<?php

namespace App\Enums;

enum TableHeaders: string
{
    // Common headers
    case Id = '#';
    case Title = 'Title';
    case Status = 'Status';
    case Actions = 'Actions';
    case Action = 'Action';
    case Created = 'Created';
    case Date = 'Date';
    case Posted = 'Posted';

    // User related
    case Name = 'Name';
    case Email = 'E-mail';
    case Company = 'Company';
    case Roles = 'Roles';
    case User = 'User';
    case Buyer = 'Buyer';
    case Seller = 'Seller';
    case Owner = 'Owner';
    case Joined = 'Joined';

    // RFQ related
    case RfqHash = 'RFQ #';
    case Rfq = 'RFQ';
    case Deadline = 'Deadline';
    case Invited = 'Invited';
    case InvitedAt = 'Invited At';
    case Items = 'Items';
    case Quotes = 'Quotes';
    case Submitted = 'Submitted';
    case SentAt = 'Sent At';

    // Quote related
    case TotalAmount = 'Total Amount';
    case ValidUntil = 'Valid Until';

    // Order related
    case OrderNumber = 'Order Number';
    case Total = 'Total';
    case Markets = 'Markets';

    // Product related
    case Sku = 'SKU';
    case Category = 'Category';
    case Price = 'Price';
    case Stock = 'Stock';
    case Products = 'Products';

    // Market related
    case Market = 'Market';
    case Location = 'Location';

    // Log related
    case Type = 'Type';
    case Message = 'Message';
    case IpAddress = 'IP Address';

    // Supplier related
    case Supplier = 'Supplier';
    case RespondedAt = 'Responded At';

    case Undefined = 'Undefined';

    /**
     * Get the translatable key for use with Laravel's translation system
     */
    public function key(): string
    {
        return $this->value;
    }

    /**
     * Get the translated label based on current locale
     */
    public function label(): string
    {
        $locale = app()->getLocale();

        return match ($locale) {
            'az' => $this->trans_to_az(),
            default => $this->trans_to_en(),
        };
    }

    /**
     * Get Azerbaijani translation
     */
    public function trans_to_az(): string
    {
        return match ($this) {
            self::Id => '#',
            self::Title => 'Başlıq',
            self::Status => 'Status',
            self::Actions => 'Əməliyyatlar',
            self::Action => 'Əməliyyat',
            self::Created => 'Yaradıldı',
            self::Date => 'Tarix',
            self::Posted => 'Yerləşdirildi',

            self::Name => 'Ad',
            self::Email => 'E-poçt',
            self::Company => 'Şirkət',
            self::Roles => 'Rollar',
            self::User => 'İstifadəçi',
            self::Buyer => 'Alıcı',
            self::Seller => 'Satıcı',
            self::Supplier => 'Təchizatçı',
            self::Owner => 'Sahibi',
            self::Joined => 'Qoşuldu',

            self::RfqHash => 'RFQ #',
            self::Rfq => 'RFQ',
            self::Deadline => 'Son tarix',
            self::Invited, self::InvitedAt => 'Dəvət olundu',
            self::Items, self::Products => 'Məhsullar',
            self::Quotes => 'Təkliflər',
            self::Submitted, self::SentAt => 'Göndərildi',

            self::TotalAmount => 'Ümumi Məbləğ',
            self::ValidUntil => 'Etibarlıdır',

            self::OrderNumber => 'Sifariş Nömrəsi',
            self::Total => 'Cəmi',
            self::Markets => 'Mağazalar',

            self::Sku => 'SKU',
            self::Category => 'Kateqoriya',
            self::Price => 'Qiymət',
            self::Stock => 'Stok',

            self::Market => 'Mağaza',
            self::Location => 'Məkan',

            self::Type => 'Tip',
            self::Message => 'Mesaj',
            self::IpAddress => 'IP Ünvanı',

            self::RespondedAt => 'Cavab verildi',
        };
    }

    /**
     * Get English translation
     */
    public function trans_to_en(): string
    {
        return $this->value;
    }

    /**
     * Create headers array for TallStackUI tables using translation
     */
    public static function make(array $columns): array
    {
        return array_map(function ($column) {
            $sortable = $column['sortable'] ?? true;
            $label = $column['label'] ?? '';

            $translatedLabel = $label instanceof self
                ? $label->label()
                : __($label);

            return [
                'index' => $column['index'],
                'label' => $translatedLabel,
                'sortable' => $sortable,
            ];
        }, $columns);
    }
}
