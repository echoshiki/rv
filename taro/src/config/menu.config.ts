import { checkLoginBeforeNavigate } from '@/utils/auth';
import useAuthStore from '@/stores/auth';
import Taro from '@tarojs/taro';
import CarIcon from '@/assets/icons/usage/car.svg';
import AgreementIcon from '@/assets/icons/usage/agreement.svg';
import HandshakeIcon from '@/assets/icons/usage/handshake.svg';
import NetIcon from '@/assets/icons/usage/net.svg';
import ElectricIcon from '@/assets/icons/usage/electric.svg';
import WaterIcon from '@/assets/icons/usage/water.svg';
import EquipmentIcon from '@/assets/icons/usage/equipment.svg';
import DashboardIcon from '@/assets/icons/usage/dashboard.svg';
import ServiceIcon from '@/assets/icons/service.svg';
import DefaultCover from '@/assets/images/cover.jpg';

const userCenterRowMenu = [
    {
        title: '我的报名',
        icon: ServiceIcon,
        link: '/pages/activity/form/index',
        onClick: ({ link }) => checkLoginBeforeNavigate(link)
    },
    {
        title: '积分商城',
        icon: ServiceIcon,
        link: '/pages/point/index'
    },
    {
        title: '积分规则',
        icon: ServiceIcon,
        link: '/pages/book/index'
    },
    {
        title: '在线客服',
        icon: ServiceIcon,
        link: '',
        onClick: () => {
            Taro.openCustomerServiceChat({
                extInfo: {url: ''},
                corpId: '',
                success: function (res) {
                    console.log(res);
                }
            });
        }
    }
];

const userCenterColumnMenu = [
    {
        title: '个人资料',
        icon: ServiceIcon,
        link: '/pages/user/profile/index',
        onClick: ({ link }) => checkLoginBeforeNavigate(link)
    },
    {
        title: '我的收藏',
        icon: ServiceIcon,
        link: '/pages/user/favorite/index',
        onClick: ({ link }) => checkLoginBeforeNavigate(link)
    },
    {
        title: '邀请好友',
        icon: ServiceIcon,
        link: ''
    },
    {
        title: '隐私政策',
        icon: ServiceIcon,
        link: '/pages/book/index'
    },
    {
        title: '用户协议',
        icon: ServiceIcon,
        link: '/pages/book/index'
    },
    {
        title: '退出登录',
        icon: ServiceIcon,
        link: '',
        onClick: () => useAuthStore.getState().logout()
    }
];

const usageRowMenu = [
    {
        title: '我的房车',
        icon: CarIcon,
        link: '/pages/activity/form/index',
        onClick: ({ link }) => checkLoginBeforeNavigate(link)
    },
    {
        title: '维保续约',
        icon: AgreementIcon,
        link: '/pages/point/index'
    },
    {
        title: '售后标准',
        icon: HandshakeIcon,
        link: '/pages/book/index'
    },
    {
        title: '售后网点',
        icon: NetIcon,
        link: '',
    },
    {
        title: '水路使用',
        icon: WaterIcon,
        link: '/pages/activity/form/index',
        onClick: ({ link }) => checkLoginBeforeNavigate(link)
    },
    {
        title: '电路使用',
        icon: ElectricIcon,
        link: '/pages/point/index'
    },
    {
        title: '设备使用',
        icon: EquipmentIcon,
        link: '/pages/book/index'
    },
    {
        title: '仪表指示',
        icon: DashboardIcon,
        link: '',
    }
];

const articleList = [
    {
        id: '1',
        title: '卫航房车西藏10天三晚自驾游活动开始报名了',
        date: '2025-01-15',
        cover: DefaultCover
    },
    {
        id: '2',
        title: '卫航房车西藏10天三晚自驾游活动开始报名了',
        date: '2025-01-15',
        cover: DefaultCover
    },
    {
        id: '3',
        title: '卫航房车西藏10天三晚自驾游活动开始报名了',
        date: '2025-01-15',
        cover: DefaultCover
    }
]

export {
    userCenterRowMenu,
    userCenterColumnMenu,
    usageRowMenu,
    articleList
}