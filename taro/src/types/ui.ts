interface UserInfo {
    id: string,
    name: string,
    phone: string,
    avatar: string
}

interface MenuItem {
    title: string,
    icon: string,
    description?: string,
    link: string,
    onClick?: (item: MenuItem) => void,
}

interface MenuList {
    menuList: MenuItem[]
}

interface Category {
    id: string,
    title: string,
    description: string,
    code: string,
    channel?: string
}

interface ArticleList {
    list: ArticleItem[]
}

interface ArticleItem {
    id: string,
    title: string,
    date: string,
    cover: string | null,
    category?: Category
}

interface ArticleDetail extends ArticleItem {
    content: string
}

interface ActivityList {
    list: ActivityItem[]
}

interface ActivityItem {
    id: string,
    title: string,
    date: string,
    cover: string | null,
    category: Category,
    description: string,
    registration_fee: string,
    max_participants: number,
    current_participants: number,
    registration_start_at: string,
    registration_end_at: string,
    started_at: string,
    ended_at: string,
    is_recommend: boolean,
}

interface ActivityDetail extends ActivityItem {
    content: string
}

interface SectionTitle {
    title: string,
    subtitle?: string,
    link?: string,
}

interface BannerItem {
    id: string,
    image: string,
    title?: string,
    link?: string
}

interface RegistrationList {
    list: RegistrationItem[]
}

interface RegistrationItem {
    id: string,
    activity_id: string,
    registration_no: string,
    name: string,
    phone: string,
    province: string,
    city: string,
    status: RegistrationStatus,
    paid_amount: string,
    payment_method: string,
    payment_time: string,
    payment_no: string,
    created_at: string,
    updated_at: string,
    activity: RegistrationActivity,
}

interface RegistrationStatus {
    label: string,
    value: string
}

interface RegistrationActivity {
    id: string,
    title: string,
    cover: string | null,
    started_at: string,
    ended_at: string,
}

interface RegionItem {
    code: string,
    name: string
}

interface RvItem {
    id: string,
    name: string,
    cover: string,
    price: string,
    order_price: string
}

interface RvList {
    list: RvItem[],
    total?: number,
    per_page?: number,
    current_page?: number,
    has_more_pages?: boolean
}

interface RvDetail extends RvItem {
    photos: string[],
    content: string
} 

export {
    SectionTitle,
    MenuItem,
    MenuList,
    UserInfo,
    ArticleItem,
    ArticleList,
    ArticleDetail,
    Category,
    ActivityItem,
    ActivityList,
    ActivityDetail,
    BannerItem,
    RegistrationItem,
    RegistrationList,
    RegistrationActivity,
    RegionItem,
    RvItem,
    RvList,
    RvDetail
}