package utils

import (
	"xapimanager/application/common"
	"xapimanager/config"
	"github.com/lestrrat/go-file-rotatelogs"
	"github.com/pkg/errors"
	"github.com/rifflock/lfshook"
	"github.com/sirupsen/logrus"
	"os"
	"path"
	"time"
)

var Log *logrus.Logger

func init() {
	Log = logrus.New()
	//设置日志格式为json格式
	Log.SetFormatter(&logrus.JSONFormatter{})
	// 设置日志级别为info以上
	Log.SetLevel(logrus.InfoLevel)
	path, _ := os.Getwd()
	logPath := path + "/" + config.GetGlobal().LOG_PATH
	// 判断日志路径是否存在(19/08/29 - 刘鸣扬)
	res, _ := common.PathExists(logPath)
	if !res {
		// 若日志路径不存在则创建目录(19/08/29 - 刘鸣扬)
		_, _ = common.CreateDir(logPath)
	}
	ConfigLocalFilesystemLogger(logPath, "std.log", time.Hour*24*7, time.Hour*24)
}
func ConfigLocalFilesystemLogger(logPath string, logFileName string, maxAge time.Duration, rotationTime time.Duration) {
	baseLogPaht := path.Join(logPath, logFileName)
	writer, err := rotatelogs.New(
		baseLogPaht+".%Y%m%d",
		rotatelogs.WithLinkName(baseLogPaht),      // 生成软链，指向最新日志文件
		rotatelogs.WithMaxAge(maxAge),             // 文件最大保存时间
		rotatelogs.WithRotationTime(rotationTime), // 日志切割时间间隔
	)
	if err != nil {
		Log.Errorf("config local file system logger error. %+v", errors.WithStack(err))
	}
	lfHook := lfshook.NewHook(lfshook.WriterMap{
		logrus.DebugLevel: writer, // 为不同级别设置不同的输出目的
		logrus.InfoLevel:  writer,
		logrus.WarnLevel:  writer,
		logrus.ErrorLevel: writer,
		logrus.FatalLevel: writer,
		logrus.PanicLevel: writer,
	}, &logrus.TextFormatter{DisableColors: true, TimestampFormat: "2006-01-02 15:04:05.000"})

	Log.AddHook(lfHook)
}
