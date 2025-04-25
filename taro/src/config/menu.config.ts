import { checkLoginBeforeNavigate } from '@/utils/auth';
import useAuthStore from '@/stores/auth';
import Taro from '@tarojs/taro';
import FormIcon from '@/assets/icons/form.svg';
import PointIcon from '@/assets/icons/point.svg';
import PointBookIcon from '@/assets/icons/point-book.svg';
import ServiceIcon from '@/assets/icons/service.svg';

import SettingIcon from '@/assets/icons/setting.svg';
import FavoriteIcon from '@/assets/icons/favorite.svg';
import InviteIcon from '@/assets/icons/invite.svg';
import AgreementIcon from '@/assets/icons/agreement.svg';
import BillIcon from '@/assets/icons/bill.svg';
import LogoutIcon from '@/assets/icons/logout.svg';

const userCenterRowMenu = [
    {
        title: '我的报名',
        icon: FormIcon,
        link: '/pages/activity/form/index',
        onClick: ({ link }) => checkLoginBeforeNavigate(link)
    },
    {
        title: '积分商城',
        icon: PointIcon,
        link: '/pages/point/index'
    },
    {
        title: '积分规则',
        icon: PointBookIcon,
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
        icon: SettingIcon,
        link: '/pages/user/profile/index',
        onClick: ({ link }) => checkLoginBeforeNavigate(link)
    },
    {
        title: '我的收藏',
        icon: FavoriteIcon,
        link: '/pages/user/favorite/index',
        onClick: ({ link }) => checkLoginBeforeNavigate(link)
    },
    {
        title: '邀请好友',
        icon: InviteIcon,
        link: ''
    },
    {
        title: '隐私政策',
        icon: BillIcon,
        link: '/pages/book/index'
    },
    {
        title: '用户协议',
        icon: AgreementIcon,
        link: '/pages/book/index'
    },
    {
        title: '退出登录',
        icon: LogoutIcon,
        link: '',
        onClick: () => useAuthStore.getState().logout()
    }
];

export {
    userCenterRowMenu,
    userCenterColumnMenu
}