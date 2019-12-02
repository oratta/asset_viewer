from mf import mf
import sys

def main():
    instance = mf()
    browser = instance.login()
    filePath = instance.loadPortfolio(browser)
    return filePath

if __name__ == "__main__":
    filePath = main()
    sys.exit(filePath)