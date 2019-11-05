from mf import mf

def main():
    instance = mf()
    browser = instance.login()
    instance.loadPortfolio(browser)

if __name__ == "__main__":
    main()