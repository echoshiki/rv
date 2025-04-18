import { View, Text, Image } from '@tarojs/components'
import { useLoad } from '@tarojs/taro'
import './index.css'

import { Swiper } from '@nutui/nutui-react-taro'

const list = [
  'https://storage.360buyimg.com/jdc-article/NutUItaro34.jpg',
  'https://storage.360buyimg.com/jdc-article/NutUItaro2.jpg',
  'https://storage.360buyimg.com/jdc-article/welcomenutui.jpg',
  'https://storage.360buyimg.com/jdc-article/fristfabu.jpg',
]

export default function Index () {
  useLoad(() => {
    console.log('Page loaded.')
  })

  return (
    <View className='index'>
      <Swiper defaultValue={1} autoPlay indicator>
      {list.map((item, index) => (
        <Swiper.Item key={item}>
          <Image
            className="w-2/3"
            mode='widthFix'
            onClick={() => console.log(index)}
            src={item}
          />
        </Swiper.Item>
      ))}
    </Swiper>
      <Text>Hello world!</Text>
    </View>
  )
}
