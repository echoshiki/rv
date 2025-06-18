import { PropsWithChildren } from 'react'
import { useLaunch } from '@tarojs/taro'
import useAuthStore from './stores/auth';
import useSettingStore from './stores/setting';
import './app.css'

function App({ children }: PropsWithChildren<any>) {
	useLaunch(() => {
		console.log('App launched.')

		// 获取全局设置
		useSettingStore.getState().fetchSettings();

		const authStore = useAuthStore.getState();
		if (!authStore.isLoggedIn()) {
			console.log('执行静默登录...');
			authStore.loginInSilence().then(isLoggedIn => {
				if (isLoggedIn) {
					console.log('登录成功，记录活跃时间...');
					authStore.updateLastActiveAt();
				}
			});
		} else {
			console.log('用户已登录，记录活跃时间...');
			authStore.updateLastActiveAt();
		}
	});

	// children 是将要会渲染的页面
	return children
}

export default App
