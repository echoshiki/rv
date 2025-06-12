import { 
    PaymentStatus, 
    PaymentParam,
    UserInfo,
    BannerItem,
    Category as ApiCategory,
    ActivityItem,
    ActivityDetail,
    RegionItem,
    RegistrationItem,
    StatusItem,
    RegistrationActivity,
    RvOrderItem,
    RvOrderList,
    PointLogItem,
    PointLogList,
    MyCarItem,
    MyCarList,
    ArticleItem,
    ArticleDetail,
    RvItem,
    RvDetail,
    RvAllData,
    BaseQueryParams
} from './api';

interface MenuItem {
    title: string,
    icon: string,
    description?: string,
    link: string,
    onClick?: (item: MenuItem) => void,
}

interface Category extends ApiCategory {
    channel?: string
}

interface SectionTitle {
    title: string,
    subtitle?: string,
    link?: string,
    theme?: 'light' | 'dark',
    type?: 'default' | 'row',
    className?: string
}

interface RegistrationFormData {
    name: string,
    phone: string,
    address: string
}

interface PaymentOptions {
    orderId: string | number;
    orderType: 'rv' | 'activity';
    amount: number;
    description?: string;
}

interface UsePaymentReturn {
    isPaying: boolean;
    paymentError: string | null;
    startPayment: (options: PaymentOptions) => Promise<PaymentParam>;
    checkPaymentStatus: (outTradeNo: string) => Promise<PaymentStatus>;
}

export {
    UserInfo,
    BannerItem,
    SectionTitle,
    StatusItem,
    MenuItem,
    ArticleItem,
    ArticleDetail,
    Category,
    ActivityItem,
    ActivityDetail,
    RegistrationItem,
    RegistrationActivity,
    RegistrationFormData,
    RegionItem,
    RvItem,
    RvDetail,
    RvAllData,
    MyCarItem,
    MyCarList,
    PointLogList,
    PointLogItem,
    BaseQueryParams,
    PaymentOptions,
    UsePaymentReturn,
    RvOrderItem,
    RvOrderList
}