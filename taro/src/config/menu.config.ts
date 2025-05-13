import { checkLoginBeforeNavigate } from '@/utils/auth';
import useAuthStore from '@/stores/auth';
import Taro from '@tarojs/taro';
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
        icon: ServiceIcon,
        link: '/pages/activity/form/index',
        onClick: ({ link }) => checkLoginBeforeNavigate(link)
    },
    {
        title: '维保续约',
        icon: ServiceIcon,
        link: '/pages/point/index'
    },
    {
        title: '售后标准',
        icon: ServiceIcon,
        link: '/pages/book/index'
    },
    {
        title: '售后网点',
        icon: ServiceIcon,
        link: '',
    },
    {
        title: '我的房车',
        icon: ServiceIcon,
        link: '/pages/activity/form/index',
        onClick: ({ link }) => checkLoginBeforeNavigate(link)
    },
    {
        title: '维保续约',
        icon: ServiceIcon,
        link: '/pages/point/index'
    },
    {
        title: '售后标准',
        icon: ServiceIcon,
        link: '/pages/book/index'
    },
    {
        title: '售后网点',
        icon: ServiceIcon,
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