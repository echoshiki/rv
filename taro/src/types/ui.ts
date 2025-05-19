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
    BannerItem
}