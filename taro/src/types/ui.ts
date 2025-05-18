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

interface ArticleItem {
    id: string,
    title: string,
    date: string,
    cover: string | null,
    category?: ArticleCategory
}

interface ArticleList {
    list: ArticleItem[]
}

interface ArticleDetail {
    id: string,
    title: string,
    content: string,
    cover: string | null,
    date: string,
    category?: ArticleCategory
}

interface ArticleCategory {
    id: string,
    title: string,
    description: string,
    code: string
}

interface ActivityItem {
    id: string,
    title: string,
    date: string,
    cover: string | null,
    category: ActivityCategory,
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

interface ActivityList {
    list: ActivityItem[]
}

interface ActivityDetail extends ActivityItem {
    content: string
}

interface ActivityCategory {
    id: string,
    title: string,
    description: string,
    code: string
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
    ArticleCategory,
    ActivityItem,
    ActivityList,
    ActivityDetail,
    ActivityCategory,
    BannerItem
}