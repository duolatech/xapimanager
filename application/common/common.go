/**
 * 公共函数类
 */
package common

import (
	"crypto/md5"
	"encoding/hex"
	"encoding/json"
	"io"
	"io/ioutil"
	"math/rand"
	"net/http"
	"os"
	"path"
	"reflect"
	"regexp"
	"strconv"
	"strings"
	"time"
)

//中文字符串截取
func SubString(str string, start int, length int, flag bool) (newStr string) {

	stringRune := []rune(str)
	if len(stringRune) > length {
		newStr = string(stringRune[start:length])
		if flag {
			newStr += "..."
		}
	} else {
		newStr = string(stringRune)
	}
	return
}

//判断变量是否为空
func IsEmpty(a interface{}) bool {
	v := reflect.ValueOf(a)
	if v.Kind() == reflect.Ptr {
		v = v.Elem()
	}
	return v.Interface() == reflect.Zero(v.Type()).Interface()
}

//判断是否是手机号
func IsPhone(phone string) (b bool) {
	if m, _ := regexp.MatchString("^1[0-9]{10,}$", phone); !m {
		return false
	}
	return true
}

//判断是否是邮箱
func IsEmail(email string) (b bool) {
	if m, _ := regexp.MatchString(`\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*`, email); !m {
		return false
	}
	return true
}

//判断元素是否在切片中
func InArray(element int, arr []string) bool {
	str := strconv.Itoa(element)
	for _, v := range arr {
		if str == v {
			return true
		}
	}
	return false
}

//格式化时间,时间戳转日期
func FormatTime(timestamp int, status int) (date string) {
	switch status {
	//1 不含小时
	case 1:
		date = time.Unix(int64(timestamp), 0).Format("2006-01-02")
	//2 含小时
	case 2:
		date = time.Unix(int64(timestamp), 0).Format("2006-01-02 15:04")
	}
	return
}

// 通过map主键唯一的特性过滤重复元素
func RemoveRepByMap(slc []int) []int {
	result := []int{}
	tempMap := map[int]byte{} // 存放不重复主键
	for _, e := range slc {
		l := len(tempMap)
		tempMap[e] = 0
		if len(tempMap) != l { // 加入map后，map长度变化，则元素不重复
			result = append(result, e)
		}
	}
	return result
}

//检查用户权限是否存在
func CheckAuth(element string, auth []string) bool {

	flag := false
	for _, v := range auth {
		if element == v {
			flag = true
		}
	}

	return flag
}

//数字字符串转整型
func StringToInt(str string) (number int) {
	number, _ = strconv.Atoi(str)
	return
}

//切片去重
func RemoveRepeatedElement(arr []string) (newArr []string) {
	newArr = make([]string, 0)
	for i := 0; i < len(arr); i++ {
		repeat := false
		for j := i + 1; j < len(arr); j++ {
			if arr[i] == arr[j] {
				repeat = true
				break
			}
		}
		if !repeat {
			newArr = append(newArr, arr[i])
		}
	}
	return
}

//json解码
func JsonDecodetoMap(jsonStr string) (data map[string]interface{}) {

	data = map[string]interface{}{}
	json.Unmarshal([]byte(jsonStr), &data)

	return
}

//从字符串中去除html标签
func StripTags(str string) string {

	//将HTML标签全转换成小写
	re, _ := regexp.Compile("\\<[\\S\\s]+?\\>")
	str = re.ReplaceAllStringFunc(str, strings.ToLower)
	//去除STYLE
	re, _ = regexp.Compile("\\<style[\\S\\s]+?\\</style\\>")
	str = re.ReplaceAllString(str, "")
	//去除SCRIPT
	re, _ = regexp.Compile("\\<script[\\S\\s]+?\\</script\\>")
	str = re.ReplaceAllString(str, "")
	//去除所有尖括号内的HTML代码，并换成换行符
	re, _ = regexp.Compile("\\<[\\S\\s]+?\\>")
	str = re.ReplaceAllString(str, "\n")
	//去除连续的换行符
	re, _ = regexp.Compile("\\s{2,}")
	str = re.ReplaceAllString(str, "\n")

	return strings.TrimSpace(str)
}

// 生成32位MD5
func MD5(text string) string {
	ctx := md5.New()
	ctx.Write([]byte(text))
	return hex.EncodeToString(ctx.Sum(nil))
}

//随机生成字符串
func GetRandomString(l int) string {
	str := "0123456789abcdefghijklmnopqrstuvwxyz"
	bytes := []byte(str)
	result := []byte{}
	r := rand.New(rand.NewSource(time.Now().UnixNano()))
	for i := 0; i < l; i++ {
		result = append(result, bytes[r.Intn(len(bytes))])
	}
	return string(result)
}

//获取文件扩展名
func GetFileSuffix(file string) (fileSuffix string) {
	filenameWithSuffix := path.Base(file)     //获取文件名带后缀
	fileSuffix = path.Ext(filenameWithSuffix) //获取文件后缀
	return
}

//创建目录
func CreateDir(path string) (bool, error) {
	exist, err := PathExists(path)
	if !exist {
		// 创建文件夹
		err := os.MkdirAll(path, os.ModePerm)
		if err != nil {
			return false, err
		} else {
			return true, nil
		}
	}
	return false, err
}

// 判断文件夹是否存在
func PathExists(path string) (bool, error) {
	_, err := os.Stat(path)
	if err == nil {
		return true, nil
	}
	if os.IsNotExist(err) {
		return false, nil
	}
	return false, err
}

//图片替换
func ReplaceImage(src string) string {

	if !strings.Contains(src, "tmp/") {
		return src
	}
	newstr := strings.Replace(src, "/tmp/", "/normal/", -1)
	dir := path.Dir(newstr)

	CreateDir("storage" + dir)

	_, err := CopyFile("storage"+newstr, "storage"+src)
	if err != nil {
		return ""
	} else {
		return newstr
	}
}

//文件复制
func CopyFile(dstName, srcName string) (written int64, err error) {
	src, err := os.Open(srcName)
	if err != nil {
		return
	}
	defer src.Close()
	dst, err := os.OpenFile(dstName, os.O_WRONLY|os.O_CREATE, 0644)
	if err != nil {
		return
	}
	defer dst.Close()
	return io.Copy(dst, src)
}

//get请求
func HttpGet(url string) (res string, err error) {
	resp, err := http.Get(url)
	if err != nil {
		return "", err
	}

	defer resp.Body.Close()
	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		return "", err
	}

	return string(body), nil
}
